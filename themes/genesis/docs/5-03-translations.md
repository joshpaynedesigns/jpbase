---
title:  Contribute to Genesis translations
menuTitle: Translations
layout: layouts/base.njk
permalink: contribute/genesis-translations/index.html
tags: docs
---

Thank you for your interest in contributing translations to the Genesis parent theme. Genesis uses [GlotPress](https://glotpress.blog/the-manual/) to maintain translations and [Traduttore](https://github.com/wearerequired/traduttore) to build language packs for download. 

## The Genesis GlotPress server

The Genesis GlotPress server is located at https://translate.studiopress.com/.

To contribute translations:
 
1. [Register for a free account](https://translate.studiopress.com/register/).
2. Log in and contribute translations to the [latest version of Genesis](https://translate.studiopress.com/global/projects/genesis-framework/genesis/).

A description of GlotPress terminology and the translation process is available in the [GlotPress manual](https://glotpress.blog/the-manual/editing-a-string/).

## Get help with the translation process

Join the <a href="{{ '/contribute/community/#genesiswp-slack-workspace' | url }}">GenesisWP Slack workspace</a> and ask questions in the #translations channel.

<a href="{{ '/contribute/community/#genesiswp-slack-workspace' | url }}" class="button">GenesisWP Slack</a>

## Genesis language packs

The Genesis GlotPress server automatically generates new language packs within five minutes of new translations being accepted. 

### Regenerate language packs manually

If packs are not updated automatically after five minutes, ask in the <a href="{{ '/contribute/community/#genesiswp-slack-workspace' | url }}">GenesisWP Slack</a> #translations channel for a language server administrator to manually update the language packs:

> Please could someone with SSH access to translate.studiopress.com manually regenerate Genesis language packs using `wp traduttore language-pack build genesis-framework/genesis`. I have waited five minutes after a translation was accepted and the packs do not seem to be updating automatically with my change. Thanks! 

### Download language packs in Genesis

Genesis 3.3 introduced the ability to download language file updates automatically.

Switch your site language in the WordPress admin at <em>Settings → General</em>, then visit <em>Dashboard → Updates</em> to see a prompt to update language files.


### Download language packs manually

Find a [list of Genesis language packs in JSON format](https://translate.studiopress.com/global/api/translations/genesis-framework/genesis/) to download packs manually if desired. Use a [JSON-to-text conversion tool](https://onlinejsontools.com/convert-json-to-text) to make the JSON easier to read if your browser does not format JSON for you. 
