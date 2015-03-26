=== Contact Form 7 Anti Spambot ===
Contributors: SzMake
Donate link: http://wp.szmake.net/donate/
Tags: token, ajax, spambot, antispam, captcha, spam, form, forms, contact form 7, contactform7, contact form, cf7, Contact Forms 7
Requires at least: 3.8.3
Tested up to: 4.1
Stable tag: 1.0.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl.html

No spam in the Contact Form 7.Add anti-spambot functionality to the CF7,it blocks spam without using CAPTCHA.

== Description ==

In English:  

No spam in the <a href='https://wordpress.org/plugins/contact-form-7/'>Contact Form 7</a>.Add anti-spambot functionality to the CF7.

It blocks spam without using CAPTCHA,blocked by Invisible internal token-code with ajax.

This plugin blocks 100% of spam post in the author of the environment.

There is no modification of display the contact form.(The added field hidden)

(but the message which is posted by spammers manually via browser is not blocked by this plugin)

This method is the same as the  <a href='https://wordpress.org/plugins/sz-comment-filter/'>Sz Comment Filter</a> which is released ahead.

In Japanese:  

このプラグインは<a href='https://wordpress.org/plugins/contact-form-7/'>Contact Form 7</a>へのスパムbotからの投稿をブロックするプラグインです。

このプラグインでは、見えない入力欄を用意して投稿時にjavascriptでCAPTCHA入力に変わる固有の確認トークン入力処理をで行うことでスパムBotからの投稿をブロックします。

作者の環境では、今のところこのプラグインで100％スパムBOT投稿がブロックできています。

利用ユーザーのコメントフォームの見え方は変わりません。(追加される入力欄は非表示でユーザーから見えません) 

(残念ながらこのプラグインではブラウザを介した手入力によるスパム投稿はブロックできません)

この対策手法は先にリリースしている <a href='https://wordpress.org/plugins/sz-comment-filter/'>Sz Comment Filter</a> と同じ方法です。

[日本語の詳細説明ページはこちら](http://wp.szmake.net/contact-from-7-add-spam-bot-filter/ "Documentation in Japanese")

= IMPORTANT NOTE =
This plugin works with ContactForm7 3.6+,or later versions.  

This plug-in is not related to the developer of the "Contact Form 7".

(ja)このプラグインはContact Form 7 バージョン3.6以降との組み合わせで動作します。

(ja)このプラグインの作者はContact Form 7プラグイン開発元とは関係ありません。ご注意下さい。

= Translators =

* Japanese (ja)


== Installation ==

You can either install it automatically from the WordPress admin, or do it manually:

1. Upload 'contact-form-7-anti-spambot' folder to the `/wp-content/plugins/` directory.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Edit a form in Contact Form 7.
1. Choose "Anti-Spambot" from the Generate Tag dropdown.
1. Insert the generated tag anywhere in your form.(The added field hidden by JavaScript and inline CSS.)

= Usage =

Insert the "Anti-Spambot" tag each CF7-form setting, it'll begin to block the spam message.
When you choose 'Contact > Contact Forms' in the admin menu, it's shown report. e.g."Total 18 spam posts were blocked."
(If the report was not shown,then please check "Screen Options" section.)

== Frequently Asked Questions ==

= How does CF7-anti-spambot plugin block the spam message? =

The blocking function is implemented by JavaScript(AJAX) and invisible 2 input fields.

= What is the first invisible input-field? =

The first input-form is input token-code by JavaScript.When submit button was pushed, AJAX goes to have token-code.
This fields is hidden by JavaScript.
The spam-bots can not set valid token-code. - the message will be blocked because it is spam-bots.

= What is the second invisible input-field? =

The second input-form is honeypot fields.this fields is hidden by css-define.
This field is hidden for the user and user will not input to it.so it's empty everytime.
But spam-bots is tricked, and something is input - the message will be rejected because it is spam-bots.

= How do I view the results? =

When you choose 'Contact Forms' in the admin menu, it's shown report. 
it is displayed count of blocked. and show the rejected post-data.(The latest 10 cases)

= Does the log data becomes too large? =

the log data are max 10 records.It's overwritten from old data.

= Can the visiter post message with JavaScript disabled browser? =

The visiter can post message without JavaScript.when must be enter token-code manualy.

== Screenshots ==

1. The reports of blocked spam-post. 
2. The display which is JavaScript disabled browser.

== Changelog ==

= 1.0.1 =
* The first release.  

= 1.0.0 =
* The first release.  

== Contact ==

email to contact[at]szmake.net  

twitter @sxmtz  



