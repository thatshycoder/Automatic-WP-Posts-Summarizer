=== Automatic WP Posts Summarizer ===
Contributors: turn2honey
Donate link: https://flutterwave.com/pay/emc-donate
Tags: summary, summarizer, AI,
Requires at least: 4.0
Tested up to: 6.0.1
Stable tag: 1.0.0
Requires PHP: 8.0
License: GNU General Public License
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

AI powered automatic posts summarization plugin for WordPress.

== Description ==

A plugin that uses Artificial Intelligence and Natural Language Processing (NLP) to generate an accurate and 
meaningful summary of posts.

== Features ==

- Automatically generate posts summary.
- Display summary on all posts or only specific posts.
- Customizable shortcode.
- Ability to also manually add summary to posts if you prefer.
- Connect to MeaningCloud API

== Shortcode ==

**Show summary in a post**

[awps]

**Show summary in a post with a custom title**

[awps title="Post Summary"]

**Show custom summary in a post**

[awps summary="I love automatic WordPress post summarizer plugin but here is the summary I prefer for this post"]

== Installation ==

1. Get your api key from [MeaningCloud](https://www.meaningcloud.com/developer/account)
2. Install the plugin via WordPress dashboard / or download the ZIP achieve from WordPress repository
3. Go to the plugin settings page ** WordPress Dashboard ** > ** Posts ** > Automatic WP Posts Summarizer
4. Paste the api key in the api key field and configure other settings to suit you.

== Customization == 

You can customize the shortcode with the following options:

*   `title`     Summary widget title.
*   `summary`   Custom summary to replace automatically generated summary with.

Example:

[awps title="Post Summary" summary="I love automatic WordPress post summarizer plugin but here is the summary I prefer for this post"]

== Frequently Asked Questions ==

= How do you generate post summary automatically? =

With the help of [MeaningCloud Text Summarization API,](https://www.meaningcloud.com/developer/summarization), 
the plugin is able to use Artificial Intelligence and Natural Language Processing(NLP) to generate posts summary.

= What if I'm not satisfied with the generated summary? = 

You can use the **summary** option in the plugin shortcode to add your preferred summary. 

Example:  [awps summary="My preferred summary"]