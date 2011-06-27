=== SoundCloud Shortcode ===
Contributors: jowagener, theophani
Donate link: http://soundcloud.com
Tags: soundcloud, flash, player, shortcode,
Requires at least: 2.5.0
Tested up to: 3.0.4
Stable tag: trunk

The SoundCloud Shortcode plugin allows you to integrate a player widget from SoundCloud into your Wordpress Blog by using a Wordpress shortcode.

== Description ==

The SoundCloud Shortcode plugin allows you to easily integrate a player widget for a track, set or group from SoundCloud into your Wordpress Blog by using a Wordpress shortcode.

Use it like that in your blog post: `[soundcloud]http://soundcloud.com/LINK_TO_TRACK_SET_OR_GROUP[/soundcloud]`
It also supports these optional parameters: width, height, params and useoembed.

The "params" parameter will pass the given options on to the player widget.

Our player accepts the following parameter options:

* auto_play = (true or false)
* show_comments = (true or false)
* color = (color hex code) will paint the play button, waveform and selections in this color
* theme_color = (color hex code) will set the background color

Examples:

`[soundcloud params="auto_play=true&show_comments=false"]http://soundcloud.com/forss/flickermood[/soundcloud]`
Embed a track player which starts playing automaticly and won't show any comments.

`[soundcloud params="color=33e040&theme_color=80e4a0"]http://soundcloud.com/forss/sets/live-4[/soundcloud]`
Embeds a set player with a green theme.

`[soundcloud height="150" width="250"]http://soundcloud.com/groups/experimental[/soundcloud]`
Embeds a group player with 150px height and 250px width.


When posting the standard SoundCloud embed code, the plugin tries to replace it with a shortcode.

About oEmbed:

oEmbed fetches the most recent widget code directly from SoundCloud, rather than using the legacy widget embed code.

In this version of the plugin, oEmbed is opt-in: the recommended setting is "true", but the default setting is "false". If you experience any problems with "true", revert to "default" or "false" and report bugs to SoundCloud.

Note: Some caching plugins may prevent the latest widget code from being fetched from SoundCloud.


== Installation ==



== Frequently Asked Questions ==


== Screenshots ==

1. This is how the player looks with default settings.
2. This is how the settings page looks.

== Changelog ==

= 1.2.2 =
* Added the option to use oEmbed

= 1.2.1 =
* Removed flash fallback HTML

= 1.2 =
* Added options page to allow blog-wide global default settings.

= 1.1.9 =
* Fix to support resources from api.soundcloud.com
* Security enhancement. Only support players from player.soundcloud.com, player.sandbox-soundcloud.com and player.staging-soundcloud.com

= 1.1.8 =
Bugfix to use correct SoundCloud player host

= 1.0 =
* First version
