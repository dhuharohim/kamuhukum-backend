<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CrossRefService
{
    protected $username;
    protected $password;
    protected $testMode = true; // Use test API endpoint in development
    protected $apiEndpoint;

    public function __construct()
    {
        $this->username = config('services.crossref.username');
        $this->password = config('services.crossref.password');
        // $this->apiEndpoint = $this->testMode ?
        //     'https://test.crossref.org/servlet/deposit' :
        //     'https://doi.crossref.org/servlet/deposit';

        $this->apiEndpoint = 'https://doi.crossref.org/servlet/deposit';
    }

    public function generateDoi($article, $frontEndUrl)
    {
        // Step 1: Generate XML metadata for the article
        $xml = $this->generateMetadataXml($article, $frontEndUrl);
        // return $xml;
        // Step 2: Send XML to CrossRef API
        return $this->submitToCrossRef($xml);
    }

    private function generateMetadataXml($article, $frontEndUrl)
    {
        // Create XML with proper namespace and schema
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>' .
            '<doi_batch version="4.4.2" xmlns="http://www.crossref.org/schema/4.4.2" ' .
            'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" ' .
            'xmlns:jats="http://www.ncbi.nlm.nih.gov/JATS1" ' .
            'xsi:schemaLocation="http://www.crossref.org/schema/4.4.2 ' .
            'http://www.crossref.org/schemas/crossref4.4.2.xsd"></doi_batch>');

        // Head section
        $head = $xml->addChild('head');
        $head->addChild('doi_batch_id', uniqid('batch_'));
        $head->addChild('timestamp', time());
        $head->addChild('depositor');
        $head->depositor->addChild('depositor_name', 'Kaya Ilmu Bermanfaat');
        $head->depositor->addChild('email_address', config('mail.from.address'));
        $head->addChild('registrant', 'Kaya Ilmu Bermanfaat');

        // Body section
        $body = $xml->addChild('body');
        $journal = $body->addChild('journal');

        // Journal metadata
        $journalMeta = $journal->addChild('journal_metadata');
        $journalMeta->addChild('full_title', 'Kaya Ilmu Bermanfaat');
        $journalMeta->addChild('abbrev_title', 'KIB');
        $issn = $journalMeta->addChild('issn', '3063-2420'); // Your journal's ISSN
        $issn->addAttribute('media_type', 'electronic');

        // Journal issue
        $journalIssue = $journal->addChild('journal_issue');
        $journalIssue->addChild('publication_date');
        $journalIssue->publication_date->addChild('year', date('Y', strtotime($article->published_date)));

        if ($article->edition) {
            $journalVolume = $journalIssue->addChild('journal_volume');
            $journalVolume->addChild('volume', $article->edition->volume);
            $journalIssue->addChild('issue', $article->edition->issue);
        }

        // Journal article
        $journalArticle = $journal->addChild('journal_article');
        $journalArticle->addAttribute('publication_type', 'full_text');

        // Titles
        $titles = $journalArticle->addChild('titles');
        if ($article->prefix !== null && $article->prefix !== 'null') {
            $titles->addChild('title', htmlspecialchars($article->prefix . ' ' . $article->title));
        } else {
            $titles->addChild('title', htmlspecialchars($article->title));
        }

        if ($article->subtitle) {
            $titles->addChild('subtitle', htmlspecialchars($article->subtitle));
        }

        // Contributors
        if ($article->authors && count($article->authors) > 0) {
            $contributors = $journalArticle->addChild('contributors');
            foreach ($article->authors as $index => $author) {
                $person = $contributors->addChild('person_name');
                $person->addAttribute('sequence', $index === 0 ? 'first' : 'additional');
                $person->addAttribute('contributor_role', 'author');
                $person->addChild('given_name', htmlspecialchars($author->given_name));
                $person->addChild('surname', htmlspecialchars($author->family_name));
                if ($author->orcid_id && $author->orcid_id !== 'undefined') {
                    $person->addChild('ORCID', $author->orcid_id);
                }
            }
        }

        // Publication date
        $pubDate = $journalArticle->addChild('publication_date');
        $pubDate->addAttribute('media_type', 'online');
        $date = new \DateTime($article->published_date);
        $pubDate->addChild('year', $date->format('Y'));
        // $pubDate->addChild('month', $date->format('m'));
        // $pubDate->addChild('day', $date->format('d'));

        // DOI data
        $doiData = $journalArticle->addChild('doi_data');
        $doiData->addChild('doi', '10.70573/' . $article->doi_request);
        $articleLink = $frontEndUrl . '/archives/view/' . $article->edition->slug . '/article/' . $article->slug;
        $doiData->addChild('resource', $articleLink);

        return $xml->asXML();
    }

    private function submitToCrossRef($xml)
    {
        try {
            $ch = curl_init();

            // Prepare the XML file with a proper name
            $tempXmlFile = tmpfile();
            fwrite($tempXmlFile, $xml);
            $tempXmlPath = stream_get_meta_data($tempXmlFile)['uri'];

            $postFields = [
                'operation' => 'doMDUpload',
                'login_id' => $this->username,
                'login_passwd' => $this->password,
                'fname' => new \CURLFile($tempXmlPath, 'application/xml', 'metadata.xml')
            ];

            curl_setopt_array($ch, [
                CURLOPT_URL => $this->apiEndpoint,
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_USERPWD => $this->username . ':' . $this->password,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: multipart/form-data'
                ],
                CURLOPT_POSTFIELDS => $postFields
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);
            fclose($tempXmlFile); // Clean up the temporary file

            if ($httpCode >= 200 && $httpCode < 300) {
                Log::info('DOI deposit submitted successfully', [
                    'response' => $response
                ]);
                return $response;
            }

            Log::error('DOI deposit failed', [
                'error' => $response,
                'status' => $httpCode
            ]);
            return $response;
        } catch (\Exception $e) {
            Log::error('Exception during DOI deposit', [
                'error' => $e->getMessage()
            ]);
            return $e->getMessage();
        }
    }
}
