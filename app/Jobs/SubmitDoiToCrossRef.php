<?php

namespace App\Jobs;

use App\Models\Article;
use App\Services\CrossRefService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SubmitDoiToCrossRef implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $articles;
    protected $frontEndUrl;

    /**
     * Create a new job instance.
     */
    public function __construct(array $articles, string $frontEndUrl)
    {
        $this->articles = $articles;
        $this->frontEndUrl = $frontEndUrl;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $crossRefService = new CrossRefService();
        $articles = Article::whereIn('id', array_column($this->articles, 'id'))
            ->with(['authors', 'edition'])
            ->get()
            ->map(function ($article) {
                $article->doi_request = collect($this->articles)->firstWhere('id', $article->id)['suffix'] ?? null;
                return $article;
            });

        if ($articles->isEmpty()) {
            $this->fail(new \Exception('No articles found'));
        }

        try {
            foreach ($articles as $article) {
                $test = $crossRefService->generateDoi($article, $this->frontEndUrl);
                Log::info('DOI generation result', [
                    'article_id' => $article->id,
                    'result' => $test
                ]);

                activity()
                    ->withProperties([
                        'result' => $test
                    ])
                    ->log('DOI generation attempt');

                sleep(1);
            }
        } catch (\Exception $e) {
            $this->fail($e);
        }
    }
}
