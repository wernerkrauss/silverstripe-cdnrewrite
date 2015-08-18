silverstripe-cdnrewrite
==========================

Rewrites all links for assets to use a cdn instead. It's not responsible to upload or sync the files anywhere. Some CDNs can do this for you.

## Requirements

* [`Silverstripe 3.1.* framework`](https://github.com/silverstripe/silverstripe-framework)
* [`Silverstripe 3.1.* CMS`](https://github.com/silverstripe/cms)

## Installation

Download and install manually or use composer.

## Configuration

You have to enable this filter manually using `CDNRewriteRequestFilter.cdn_rewrite` config var.
Also define `CDNRewriteRequestFilter.cdn_domain` with protocol and host.

Your config.yml might look like:

```yml
CDNRewriteRequestFilter:
  cdn_rewrite: true #global switch
  cdn_domain: 'http://cdn.mydomain.com'
  rewrite_assets: true  #rewrite stuff in assets
  rewrite_themes: false #do not rewrite stuff in themes folder
```

