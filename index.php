<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <title>Revue de presse</title>
    <meta name="viewport" content="width=device-width, initial-scale=1;" />
    <style>
      :root {
        --back-flux: #141414;
        --back-category: #1d1d1d;
        --color-visited: #555;
        --color-text: #ccc;
        --gap: clamp(10px, 1.5vw, 20px);
        --min-content: 100%;
      }

      @media (min-width: 450px) {
        :root {
          --min-content: 400px;
        }
      }

      @font-face {
        font-family: "Roboto";
        font-style: normal;
        font-weight: 400;
        src: local("RobotoCondensed"),
          url(lib/font/roboto-condensed/RobotoCondensed-Light.ttf)
            format("truetype");
      }

      * {
        box-sizing: border-box;
      }

      body {
        margin: 0;
        font-family: Roboto;
        font-weight: 400;
        font-size: 18px;
        line-height: 1.2em;
        color: var(--color-text);
        background: var(--back-category);
      }

      .categorie {
        background: var(--back-category);
        padding: var(--gap);
        border-bottom: 1px dashed var(--color-visited);
        display: grid;
        grid-template-columns: repeat( auto-fit, minmax(var(--min-content), 1fr) );
        gap: var(--gap);
      }

      .categorie h2 {
        margin: 0;
        padding: 5px 0;
        color: var(--color-visited);
        grid-column: -1 / 1;
      }

      .flux {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 0.4em;
        padding: 0;
        background: var(--back-flux);
        border-radius: 10px 0 0;
        aspect-ratio: 2 / 1;
        overflow: auto;
      }

      .flux h3 {
        position: relative;
        margin: 0 0 5px;
        padding: 10px 15px;
        border-bottom: 1px dashed var(--color-visited);
      }

      .flux a {
        position: relative;
        display: flex;
        padding: 0 10px 0 30px;
        text-decoration: none;
        color: inherit;
      }

      .flux a:before {
        content: "➤";
        position: absolute;
        top: 0;
        left: 11px;
        font-size: 10px;
        font-family: arial;
        opacity: 0.5;
      }

      .flux a:hover {
        text-decoration: underline;
      }

      .flux a:visited {
        color: var(--color-visited);
      }

      .flux h3::after {
        content: '';  
        width: 15%;
        height: 1px;
        background: var(--color-text);
        position: absolute;
        bottom: -1px;
        left: 0;
        box-sizing: border-box;
        animation: loader 1.2s ease-in-out infinite alternate;
      }

      [data-loaded] h3::after {
        display: none;
      }

      @keyframes loader {
        0% {
          left: 0;
          transform: translateX(0%);
        }
        100% {
          left: 100%;
          transform: translateX(-100%);
        }
      }
    </style>

    <link rel="shortcut icon" type="image/png" href="rss.png" />
    <script>

      const sources = <?=json_encode(include './sources.php')?>;
      const num = 20

      document.addEventListener('click', (e)=>{
        if(e.target.tagName === 'A') {
          e.target.target = '_blank'
        }
      })

      const load = async function () {
        document.body.innerHTML = ''

        for(const [categorie, rss] of Object.entries(sources)) {
          const rssElements = []
          for(const [nom, url] of Object.entries(rss)) {
            rssElements.push(`<div class="flux" data-name="${nom}" data-url="${url}"><h3>${nom}<span class="loader"></span></h3></div>`)
          }
          document.body.innerHTML += `<div class="categorie"><h2>${categorie}</h2>${rssElements.join('')}</div>`
        }

        for(const flux of document.querySelectorAll('.flux')) {
          const url = `proxy/${btoa(flux.dataset.url)}`
          try {
            const response = await fetch(url)
            const text = await response.text()
            const xml = new window.DOMParser().parseFromString(text, 'text/xml')
            const entries = Array.from(xml.querySelectorAll('item, entry')).slice(0, num)
            for(const entry of entries) {
              const title = entry.querySelector('title')?.textContent || '[Title not found]'
              let link = entry.querySelector('link')?.textContent
              if( link === '') {
                link = entry.querySelector('link').getAttribute('href')
              }
              flux.innerHTML += `<a href="${link}">${title}</a>`
            }
          } catch(e) {
            flux.innerHTML += '<div class="error>Erreur de chargement</div>'
            console.error(`Erreur de chargement pour ${flux.dataset.name}`)
          } finally {
            flux.dataset.loaded = 'true'
          }

        }
      };

      document.addEventListener('DOMContentLoaded', load)

      setInterval(load, 60 * 60 * 1000);
    </script>
  </head>

  <body></body>
</html>
