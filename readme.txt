=== Smiling Video player and video content ===
Contributors: techadminsmilingvideo
Tags: video
Requires at least: 4.6
Requires PHP: 5.6
Tested up to: 6.2.2
Stable tag: 1.1.9
License: GPL v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Smiling Video offers a video player with built-in premium video content of sport, news, cinema, music and many others.

== Description ==
Smiling.Video is a “third generation” web platform for the publishing and distribution of high quality videos of breaking news, sport, entertainment, fashion, travel, cooking, etc.

Smiling.Video supplies high quality videos of different categories – sports, news, cinema, movie & Tv, music etc – to publishers directly inside the WordPress articles. Or creating a personal videogallery. Everything *without* fixed costs for the editor.

To all content providers – whether you are a Sport League, concert organizer, video journalist or videomaker – Smiling.Video allows you to earn through your video distribution over hundred of italian websites and worldwide.

Service is available here: https://platform.smiling.video for publishers and content providers. Feel free to register as company or as individual and get access to videos.


As content provider, you are allowed to upload your videos and get a revenue share on your video views.
As publisher, you are allowed to get the HTML snippet code for embedding the video player in your site pages and get a revenue share on video views too.

This WordPress plugin is a tool useful to publishers for automatically embedding HTML snippet code into wordpress pages during editing with no need to copy/paste snippet code from smiling video platform to WordPress page editor.

The plugin is based on a Web Service API provided by https://platform.smiling.video. Such API suggests a list of video related to the current article content. The only mandatory property is the article title.

== Installation ==

Steps:

- go to https://platform.smiling.video and register ("REGISTRATI") a new account as publisher ("Sono un editore")
- fill in personal data such as email, password; check legal term acceptance and provide other information
- wait for registration email and click link to confirm
- wait for Smiling Video approval email
- now you have valid username and password credentials needed for proper plugin usage

- log in your WordPress admin console
- select "Plugin" tab
- select "Add New" plugin
- search "Classic Editor"
- select "install now"
- search "Smiling Video"
- select "install now"

== Plugin Configuration ==

- after install you have a new menu item called "Smiling Video"
- in such window, fill in username ("Smiling Video User") and password ("Smiling Video Password") fields.
- press "Salva le modifiche" to save them

== Plugin usage ==

- go to WordPress page editor
- fill in page title (i.e. "Star Wars")
- press button "Smiling Video"
- a window containing the gallery of suggested videos will appear
- press "inserisci codice" to select one of them
- a code snippet marked between [smiling_video] and [/smiling_video] will appear. Do not change it.
- continue editing the page adding article text or other videos
- save the page and publish it
- you will see the video player showing the selected video preview 

Good editing and enjoy a full set of videos!

== Screenshots ==
1. Registration Web Site
2. Plugin Configuration Page
3. Smiling Video Button in Editor
4. Suggested video list

== Frequently Asked Questions ==
= Q1. Is smiling video a pay-based service? =
No, Smiling.Video is a free service for publishers and content providers that offers video content, hosting and video player. 

= Q2. I downloaded the plugin but I cannot see/post the videos =
To be able to insert videos inside your articles you must first register on the Smiling.Video platform (to access and register [click here](https://platform.smiling.video/SmilingVideoCMS/index.htm)). After registering - or if you already have a Smiling.Video account - you will need to activate the plugin in the Wordpress plugin section, so you can see the initial configuration page. Once you have done this, follow the directions and log in with your credentials (registration on Smiling.Video platform). After saving changes you can publish your first video.

= Q3. What type of videos are available on the platform? =
Smiling.Video offers hundreds of high quality video content, from certified and qualified sources. Smiling.Video, has organized videos into different categories to meet all editorial needs: you can find news, politics, economy, cinema & television, entertainment, gossip, music, cooking, sports, football and more.

= Q4. How can I publish Smiling videos? =
Smiling allows you to choose different ways of publishing.

* **Manual publication**: the system will suggest through the text editor, a particular video to be inserted into the article in two different ways: 
  1. Once you have indicated the keyword in the title, click on the _Suggested video_ button. At that point, Smiling.Video offers a selected list of titles related to the article title.
  2. By choosing the button _All the videos_, you can access the entire video catalog (in chronological order) in which you can choose from thousands of videos in the database.

* **Automatic publication**: our system will automatically insert a video in each article based on the topic, or based on the editorial criteria you previously set on the Platform (https://platform.smiling.video platform).

= Q5. What is the video player size? =
Our video player is dynamic: it does not have a fixed size as it automatically adapts to different screens and sizes (mobile, desktop, tablet).

= Q6. Can I earn money with Smiling.Video? =
Smiling.Video offers the opportunity to earn through the insertion of promotional ads before the video: the standard pre-roll format. 

 For more information about us, please contact Smiling.Video’s team.
 
== Changelog ==

= 1.2.0 = 
 * modified API endpoint settings

= 1.1.9 = 
 * small changes

= 1.1.8 = 
 * made Classic Editor required

= 1.1.7 = 
 * plugin permission fix

= 1.1.5 = 
 * restored video-js lib
 
= 1.1.4 = 
 * added authorization for non admin users
 
= 1.1.0 = 
 * added instructions in configuration page
 * added language filter in gallery
 * added preview on all videos
 
= 1.0.0 = 
 * stable release with bug fixes

= 0.0.4 =
 * added video catalog feature

= 0.0.1 =
 * first public version including video suggest feature

 
== Upgrade Notice ==
No upgrade available yet