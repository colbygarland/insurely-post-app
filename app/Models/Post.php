<?php

namespace App\Models;

use App\Utils\LinkedInApi;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Post extends Model
{
    private static $wordpressUrl = 'https://insurely.ca/wp-json/wp/v2/posts?per_page=25';

    // The max number of posts to send to LinkedIn at one time
    private static $maxLimit = 5;

    protected $fillable = [
        'wordpress_id',
        'date',
        'date_gmt',
        'modified',
        'modified_gmt',
        'slug',
        'status',
        'type',
        'link',
        'title',
        'summary',
        'thumbnail_url',
        'published_at',
        'quote_author',
        'quote_job_title',
        'quote_company',
        'quote_body',
    ];

    private static function getThumbnailUrl(string $source)
    {
        // Get the thumbnail from the Yoast information.
        // Doing this prevents making a second call to get the image!
        $pattern = '/"thumbnailUrl":"(https?:\/\/[^"]+)"/';
        $thumbnailUrl = 'https://example.com';
        if (preg_match($pattern, $source, $matches)) {
            $thumbnailUrl = $matches[1];
        }

        return $thumbnailUrl;
    }

    public static function updateOrCreateFromWordpressPost(array $wordpressPost): Post
    {
        return self::updateOrCreate(
            ['wordpress_id' => $wordpressPost['id']],
            [
                'date' => $wordpressPost['date'],
                'date_gmt' => $wordpressPost['date_gmt'],
                'modified' => $wordpressPost['modified'],
                'modified_gmt' => $wordpressPost['modified_gmt'],
                'slug' => $wordpressPost['slug'],
                'status' => $wordpressPost['status'],
                'type' => $wordpressPost['type'],
                'link' => $wordpressPost['link'],
                'title' => self::clean($wordpressPost['title']['rendered']),
                'summary' => self::clean($wordpressPost['acf']['tldr_summary_html'], true),
                'thumbnail_url' => self::getThumbnailUrl($wordpressPost['yoast_head']),
                'quote_author' => $wordpressPost['acf']['quote_1_full_name'] ?? null,
                'quote_job_title' => $wordpressPost['acf']['quote_1_job_title'] ?? null,
                'quote_company' => $wordpressPost['acf']['quote_1_company_name'] ?? null,
                'quote_body' => $wordpressPost['acf']['quote_1_quote_text'] ?? null,
            ]
        );
    }

    public function markPublished()
    {
        $this->published_at = now();
        $this->save();
    }

    private static function clean(string $input, bool $addFinalPeriod = false)
    {
        // Decode HTML entities
        $decoded = html_entity_decode($input, ENT_QUOTES | ENT_HTML5);

        // Split into sentences using newline or list markers as delimiters
        $sentences = preg_split('/\r\n|\r|\n|<li>|<\/li>|<ul>|<\/ul>/', $decoded);

        // Remove empty lines and trim extra whitespace
        $sentences = array_filter(array_map('trim', $sentences));

        // Combine into a single formatted paragraph
        $string = implode(".\n\n", $sentences);

        // Add a final period to the end
        if ($addFinalPeriod) {
            $string .= '.';
        }

        // Remove HTML tags
        return strip_tags($string);
    }

    public function getSummary()
    {
        $summary = $this->summary;

        // Append the quote
        /** @disregard */
        $verbage = str_contains(strtolower($this->quote_job_title), 'owner') ? 'of' : 'from';
        $summary .= "\n\n\"$this->quote_body\", says $this->quote_author, $this->quote_job_title $verbage $this->quote_company";

        // Append the Link to the Article
        $summary .= "\n\nRead the full article here: $this->link";

        // Add the hashtags

        return $summary;
    }

    public static function fetchFromWordpress()
    {
        $posts = Http::get(self::$wordpressUrl); // TODO: paginate?
        Log::debug('Response from wordpress: '.json_encode($posts, JSON_PRETTY_PRINT));
        // loop through these and create a model in the DB for them, if they don't already exist, based on the wordpress_id.
        $posts_created = [];
        foreach ($posts->json() as $postData) {
            array_push($posts_created, Post::updateOrCreateFromWordpressPost($postData));
        }
    }

    public static function postsToBeSent()
    {
        return Post::where('published_at', null)->limit(self::$maxLimit)->get();
    }

    public static function postToLinkedIn(?Post $post = null)
    {
        if ($post) {
            $posts = [$post];
        } else {
            // Get the latest posts that have not been published yet
            $posts = Post::postsToBeSent();
        }

        foreach ($posts as $post) {
            try {
                LinkedInApi::createSharePost($post);
                // Mark the post as published
                $post->markPublished();
            } catch (Exception $e) {
                Log::error('Error creating post: '.$e->getMessage());

                return false;
            }
        }

        return true;
    }
}
