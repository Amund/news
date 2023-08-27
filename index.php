<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Actus</title>
    <meta name="viewport" content="width=device-width, initial-scale=1;" />
    <style>
      @font-face {
        font-family: "Roboto";
        font-style: normal;
        font-weight: 400;
        src: local("RobotoCondensed"), local("RobotoCondensed-Regular"),
          url(lib/font/roboto-condensed/RobotoCondensed-Regular.ttf)
            format("truetype");
      }

      * {
        box-sizing: border-box;
      }

      body {
        margin: 0;
        /*font:13px/16px Roboto,sans-serif;*/
        font-family: Roboto;
        color: #555;
      }

      .categorie {
        background: #ddd;
        margin: 0;
        padding: 2% 3%;
        border-bottom: 1px dashed #fff;
        /*box-shadow:0 0 50px rgba(0,0,0,0.3) inset;*/
      }

      .categorie h2 {
        margin: 10px 5px 5px;
      }

      .flux {
        display: inline-block;
        vertical-align: top;
        margin: 0.5%;
        padding: 0;
        background: #fff;
        border-radius: 10px 0 0;
        width: 24%;
        height: 200px;
        overflow: auto;
        /*box-shadow:-10px 0 30px rgba(0,0,0,0.1) inset, 0 3px 6px rgba(0,0,0,0.1);*/
      }

      .flux h3 {
        margin: 0 0 5px;
        padding: 10px 10px 7px;
        border-bottom: 1px dashed #ddd;
      }

      .flux a {
        position: relative;
        display: block;
        padding: 2px 10px 2px 30px;
        text-decoration: none;
        color: #444;
      }

      .flux a:before {
        content: "►";
        position: absolute;
        top: 2px;
        left: 10px;
        font-size: 10px;
        font-family: arial;
        opacity: 0.5;
      }

      .flux a:hover {
        text-decoration: underline;
      }

      .flux a:visited {
        color: #aaa;
      }

      @media (max-width: 1600px) {
        .flux {
          display: inline-block;
          width: 32.33%;
        }
      }

      @media (max-width: 1024px) {
        .flux {
          display: inline-block;
          width: 49%;
        }
      }

      @media (max-width: 800px) {
        .flux {
          display: block;
          width: auto;
          height: auto;
        }
      }
    </style>

    <link rel="shortcut icon" type="image/png" href="rss.png" />
    <script src="jquery-1.8.2.min.js"></script>
    <script>
      var num = 10;

      var flux = {
        Actualités: {
          Numérama: "https://www.numerama.com/feed/atom",
          "Presse-citron": "http://www.presse-citron.net/feed/",
          "Le journal du Geek": "http://www.journaldugeek.com/feed/",
          Korben: "https://korben.info/feed",
          "Tom's Guide":
            "http://www.tomsguide.fr/feeds/rss2/tom-s-guide-fr,20-0.xml",
          Clubic: "http://www.clubic.com/articles.rss",
          Framasoft: "http://www.framablog.org/index.php/feed/atom",
          Phoronix: "https://www.phoronix.com/rss.php",
          LinuxFR: "http://linuxfr.org/news.atom",
          Standblog: "http://standblog.org/blog/feed/rss2",
          FrAndroid: "https://www.frandroid.com/feed/atom",
          "Le monde informatique":
            "http://www.lemondeinformatique.fr/flux-rss/rss.xml",
          Minimachines: "http://www.minimachines.net/feed/atom",
        },
        "Développement (général)": {
          "Mozilla Hacks": "https://hacks.mozilla.org/feed/",
          "Mozilla Gfx": "https://mozillagfx.wordpress.com/feed/",
          "Filament Group": "https://www.filamentgroup.com/lab/atom.xml",
          alsacreations: "http://www.alsacreations.com/rss/actualites.xml",
          "A list apart": "https://alistapart.com/main/feed",
          quirksmode: "http://www.quirksmode.org/blog/atom.xml",
          JavascriptJabber: "http://feeds.feedburner.com/JavascriptJabber",
          "mediumequalsmessage.com":
            "http://feeds.feedburner.com/mediumequalsmessage/cwebbdesign",
          DailyJS: "http://feeds.feedburner.com/dailyjs",
          "Javascript Weekly": "http://javascriptweekly.com/rss",
          "HTML5 Weekly": "http://html5weekly.com/rss",
        },
        "Développement (techno/lib/framework)": {
          Wordpress: "https://wordpress.org/news/feed/",
          'Wordpress dev': 'https://developer.wordpress.org/news/feed/',
          Drupal: "https://www.drupal.org/section-blog/2603760/feed",
          "Drupal8 (Bugs)":
            "https://www.drupal.org/project/issues/search/drupal/rss?status[0]=1&status[1]=13&status[2]=8&status[3]=14&status[4]=4&priorities[0]=400&categories[0]=1&categories[1]=2&categories[2]=5&version[0]=8.x",
          Spip: "http://blog.spip.net/spip.php?page=backend",
          Symfony: "https://feeds.feedburner.com/symfony/blog",
          Rollup: "https://github.com/rollup/rollup/releases.atom",
          Brunch: "https://github.com/brunch/brunch/releases.atom",
          Svelte: "https://svelte.dev/blog/rss.xml",
          VueJS: "https://medium.com/feed/the-vue-point",
          React: "https://reactjs.org/feed.xml",
        },
        Logiciels: {
          Capyloon: "https://capyloon.org/releases.xml",
          Deno: "https://github.com/denoland/deno/releases.atom",
          NodeJS: "http://blog.nodejs.org/feed/",
          Docker: "https://www.docker.com/blog/feed/",
          Rust: "https://blog.rust-lang.org/feed.xml",
          "Google Chrome Releases":
            "http://feeds.feedburner.com/GoogleChromeReleases",
        },
      };

      $("body").on("click", "a", function () {
        $(this).attr("target", "_blank");
      });

      var load = function (categorie, nom, url) {
        var container = $('<div class="flux"><h3>' + nom + "</h3></div>");
        categorie.append(container);

        $.ajax({
          type: "GET",
          url: "proxy/" + btoa(url), //encodeURIComponent(url),
          cache: false,
          dataType: "xml",
          success: function (xml) {
            //console.log( xml );
            $(xml)
              .find("item,entry")
              .slice(0, num)
              .each(function () {
                var title = $(this).children("title").text();
                var link = $(this).children("link").text();
                if (link == "") link = $(this).children("link").attr("href");
                var item = $('<a href="' + link + '">' + title + "</a>");
                container.append(item);
              });
          },
          error: function () {
            console.log("erreur: " + url);
          },
        });
      };

      var loadPage = function () {
        $("body").html("");
        $.each(flux, function (categorie, rss) {
          var container = $(
            '<div class="categorie"><h2>' + categorie + "</h2></div>"
          );
          $("body").append(container);

          $.each(rss, function (nom, url) {
            load(container, nom, url);
          });
        });
      };

      $(document).ready(loadPage);

      setInterval(loadPage, 60 * 60 * 1000);
    </script>
  </head>

  <body></body>
</html>
