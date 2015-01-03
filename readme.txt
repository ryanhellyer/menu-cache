=== Menu  Cache ===
Contributors: ryanhellyer
Tags: cache, menu, nav, navigation, wpnav
Donate link: https://geek.hellyer.kiwi/donate/
Requires at least: 4.1
Tested up to: 4.2
Stable tag: 1.0


Caches WordPress nav menus.

== Description ==

= Features =
Caches the output of your menus. That's it ;)

= Why should I use this plugin? =
If you want to cache your menu content obviously, but specifically if you want to cache your menu content <strong>and</strong> keep the current/parent/child page classes working. Most menu caching systems do not cater for keeping the various classes working, whereas this plugin does.

= Why shouldn't I use this plugin? =
You should not use this plugin if you have a huge number of posts/pages on your site. The plugin caches each menu individually for each post. This can result in a huge number of cached menus on some sites, which could actually resulted in a slower site in those situations. It could also overload your object cache backend if it has insufficient memory to store them all.


== Installation ==

1. Upload the complete 'menu-cache' folder to the '/wp-content/plugins/' directory OR install via the plugin installer
2. Activate the plugin through the 'Plugins' menu in WordPress
4. That's it. The plugin is now working :)

Visit the <a href="https://geek.hellyer.kiwi/plugins/menu-cache/">Unique Headers Plugin</a> for more information.


== Frequently Asked Questions ==

= Can I use this plugin to cache my widget areas too? =
Nope. But luckily for you, <a href="http://kaspars.net/">Kaspars Dambis</a> has created the <a href="https://wordpress.org/plugins/widget-output-cache/">Widget Output Cache plugin</a> which does that for you.

= Why isn't this baked into core WordPress? =
Because it is not a good idea to cache a fresh menu for each page on some websites. If you don't have many pages, then this is a perfectly acceptable way to handle menu caching. But if you have a large site with many pages, then there may be too many menus to efficiently cache.

= Why haven't you added language support? =
Because the plugin doesn't have any text strings. There's nothing to translate ;)

= Where is the options page? =
There isn't one. Just activate it and it should start working immediately.

= Should I use this as well as WP Super Cache or W3 Total Cache? =
Maybe. Those plugins do full static page caching. Caching your menus is only useful if your visitors are able to bypass the full page cache (this usually occurs when people comment or are logged in).

= Your plugin doesn't work! =
It does work. If you think it is not working for some reason, please <a href="https://geek.hellyer.kiwi/contact/">let me know why</a>. Just because you can't see any changes on your site, or it doesn't "feel" faster does not mean that it is not working. In fact if you use the plugin incorrectly, your site could actually load more slowly than before. Caching is a complex thing and it can backfire on you if you don't do it right.


= Does it work in older versions of WordPress? =

Probably, but I only actively support the latest version of WordPress. Support for older versions is purely by accident.


= I need custom functionality. Can we pay you to build it for us? =

No, I'm too busy. Having said that, if you are willing to pay me a small fortune then I could <a href="https://geek.hellyer.kiwi/contact/">probably be persuaded</a>. I'm also open to suggestions for improvements, so feel free to send me ideas and if you are lucky, it may be added for free :)


== Changelog ==

Version 1.0: Initial release<br />


= Credits =

Thanks to the following for help with the development of this plugin:<br />
* <a href="http://kaspars.net/">Kaspars Dambis</a> - Provided inspiration for this plugin via his <a href="https://wordpress.org/plugins/widget-output-cache/">Widget Output Cache plugin</a><br />
