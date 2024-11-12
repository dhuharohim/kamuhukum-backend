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

    protected $articleIds;
    protected $frontEndUrl;

    /**
     * Create a new job instance.
     */
    public function __construct(array $articleIds, string $frontEndUrl)
    {
        $this->articleIds = $articleIds;
        $this->frontEndUrl = $frontEndUrl;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $crossRefService = new CrossRefService();
        $articles = Article::whereIn('id', $this->articleIds)
            ->with(['authors', 'edition'])
            ->get();

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
