<?php

declare(strict_types=1);

/**
 * https://devdojo.com/devdojo/simple-laravel-route-testing.
 */

namespace Modules\Xot\Tests\Feature;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Tests\TestCase;

class RouteDomTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @test
     */
    public function routes(): void
    {
        $urls = [
            // '/it/menus/emergenza-coronavirus/pages/aggiornamenti',
            // '/'.App::getlocale().'/home', //questo url mi da errore
        ];
        // dd(get_class_methods($this));
        $this->checkLinks($urls);
        static::assertTrue(true);
    }

    public function checkLinks(array $urls, int $depth = 0): void
    {
        $base_url = env('APP_URL');

        if ($depth > 4) {
            return;
        }
        $i = 0;

        foreach ($urls as $url) {
            /*
            if ($i++ > 3) {
                return;
            }
            */
            $url = str_replace('index.php', '', $url);
            if (null === $url) {
                throw new \Exception('url is null');
            }
            if (! \is_string($url)) {
                throw new \Exception('url is not a string');
            }
            $response = $this->get($url);
            $html = $response->getContent();
            if (false === $html) {
                throw new \Exception('cannot get content');
            }
            // dd(get_class_methods($response));
            // dd($response->streamedContent());The response is not a streamed response
            $status = (int) $response->status();
            if (! \in_array($status, [200, 302], true)) {
                echo $base_url.$url.' (FAILED) did not return a 200 or 302 ['.$response->status().'].'.\chr(13);
                // dd($base_url.$url);
                static::assertTrue(false);
            } else {
                echo $base_url.$url.' (success ?)'.\chr(13);
                static::assertTrue(true);
            }
            echo PHP_EOL;

            $dom = $this->dom($html);
            // $links = $dom->filter('a')->links();
            $links = $dom->filter('a')->each(
                function ($node) {
                    return $node->attr('href');
                }
            );
            $links = collect($links)->filter(
                function ($item) {
                    return ! Str::startsWith($item, 'mailto:')
                        && ! Str::startsWith($item, 'https://mail.')
                        && Str::startsWith($item, '/');
                }
            )->all();

            $this->checkLinks($links, $depth + 1);
        }
    }

    /*
    The URL of the element is relative,
    so you must define its base URI passing an absolute URL to the constructor of the
    "Symfony\Component\DomCrawler\AbstractUriElement" class ("" was passed)
    */
    private function dom(string $html): \Symfony\Component\DomCrawler\Crawler
    {
        $dom = new \Symfony\Component\DomCrawler\Crawler();
        $dom->addHTMLContent($html, 'UTF-8');

        return $dom;
    }
}
