<!DOCTYPE html>
<html>
<head>
<title>Help - NOOP</title>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.0/styles/railscasts.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.0/highlight.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.0/languages/php.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/highlight.js/8.0/languages/html.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/marked/0.3.1/marked.min.js"></script>

<style>
* { box-sizing:border-box; }
html { background:#111; }
body { max-width:960px; margin:auto; font:0.9em/1.5em tahoma,arial,sans-serif; color:#888; word-spacing: 0.08em; }
h1,h2,h3,h4,h5,h6 { font-weight:normal; color:#ccc; font-family:"Courier New", Courier, monospace; color:#fff; line-height:1em; }
h1 { font-size:5em; text-align:center; margin:0; padding:1.5em; color:#fff; }
h2 { font-size:3em; margin:0; padding:50px 0px 15px; color:#fff; border-bottom:5px solid #333; }
h3 { font-size:1.8em; margin:0; padding:30px 0px 15px; border-bottom:1px solid #333; }
h4 { font-size:1.4em; margin:0; padding:20px 0px 15px; color:#888; font-weight:bold; }
h6 { font-size:1em; margin:0; padding:10px 0 0; }
a { color:#fff; }
a[href="#"] { position:relative; top:0.5em; float:right; font-size:30px; text-decoration:none; }
p { margin:10px 30px; text-align:justify; }
ul { margin:10px 30px; }
li { margin:0 0 8px 0 }
code { font-size:1.3em; overflow:auto; padding:0 4px; background:#222; color:#aaa; }
code.hljs { padding:20px 25px; border:1px solid #444; margin:30px; }
table { margin:auto; }
td, th { padding:0.1em 0.8em; }

#toc { position:fixed; top:0; left:0; padding:0.5em 1em;  border:1px solid #333; background:#222; box-shadow:0 0 15px rgba( 16,16,16,1 ); max-height:100%; max-width:70%; overflow:auto; }
#toc a { display:none; text-decoration:none; }
#toc:hover a { display:block; }
#toc a:hover { text-decoration:underline; }
#toc [rel=H3] { font-size:0.85em; padding-left:2em;}
</style>
</head>
<body>

<div id="toc"></div>
<div id="html"></div>

<script id="md" type="text/x-markdown; charset=UTF-8"><?=file_get_contents( 'README.md' )?></script>

<script>

function toc() {
	var toc = document.querySelector( '#toc' );
	toc.innerHTML = '<div>ToC</div>';
	var h = document.querySelectorAll( 'h2,h3' );
	console.log( h );
	for( var el of h ) {
		console.log( el );
		toc.innerHTML += '<a rel="'+el.tagName+'" href="#'+el.id+'">'+el.textContent.slice(0, -2)+'</a>';
	}
}

var md = document.getElementById( 'md' );
var html = document.getElementById( 'html' );
html.innerHTML = marked( md.innerHTML );

toc();

hljs.initHighlightingOnLoad();

</script>

</body>
</html>