<?php
namespace App\Interface;

use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.sitemap')]
interface SiteMapInterface{
/**
 * Retrieves an array of sitemap URLs and their last modification dates.
 *
 * The returned array should contain elements structured as follows:
 * 
 * - 'loc': The URL location (string).
 * - 'lastmod': The last modified date of the URL (DateTime or DateTimeImmutable object).
 *
 * Example return value:
 *
 * [
 *     [
 *         'loc' => '/mypage',
 *         'lastmod' => new \DateTime()
 *     ],
 *     // Additional URLs can be added here.
 * ]
 *
 * @return array An array of arrays, each containing 'loc' and 'lastmod' keys.
 */
    public function getSiteMap(): array;
}