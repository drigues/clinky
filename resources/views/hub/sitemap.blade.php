<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach($sites as $site)
  <url>
    <loc>{{ $site['url'] }}</loc>
    <changefreq>{{ $site['changefreq'] }}</changefreq>
    <priority>{{ $site['priority'] }}</priority>
  </url>
@endforeach
</urlset>
