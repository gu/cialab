
Simple Drag and Drop

Instructions: grab anything and drag it in to the drop zone below. I've included some text below, but you can drag urls, bookmarklets, files, anything.

Check out the functionality in different browsers, because the same content appears differently when dropped. Something to watch out for in the future.

Change the options below to see the difference between the default Text and sniffing for data (not supported in IE I'm afraid).

Try also dropping a few files from your desktop on the drop zone and notice the content-type: text/uri-list
getData('Text')
getData(e.dataTransfer.types[0]) based on detected content type

Remy Sharp My name is Remy Sharp (@rem on Twitter and my blog). I run a small business in Brighton, UK called Left Logic and am running the Full Frontal JavaScript Conference and I specialise in bespoke front-end development & backend.

Drop here for info about the dragged item
HTML5 demos/@rem built this/view source Fork me on GitHub

<!DOCTYPE html>
<html>
<head>


<meta charset="utf-8">
<meta name="viewport" content="width=620">
<title>HTML5 Demo: Simple Drag and Drop</title>
<link rel="stylesheet" href="/css/html5demos.css" type="text/css">
<script src="/js/h5utils.js"></script></head><body>
<section id="wrapper">
    <header>
      <h1>Simple Drag and Drop</h1>
    </header>
<style>
article div {
  margin: 10px 0;
}

label {
  line-height: 32px;
}

/* for safari */
*[draggable=true] {
  -khtml-user-drag: element;
  cursor: move;
}

#drop {
  border: 3px dashed #ccc;
  padding: 10px;
  background: #fff;
  min-height: 200px;
/*  overflow-y: auto;*/
}

#drop .info {
  color: #999;
  text-align: center;
}

#drop ul {
  margin: 0;
  padding: 0;
}

#drop li {
  border-top: 2px solid #ccc;
  list-style: none;
  padding: 5px;
  font-size: 90%;
}

#drop li:first-child {
  border-top: 0;
}

</style>
    <article>
      <section>
        <p>Instructions: grab <em>anything</em> and drag it in to the drop zone below. I've included some text below, but you can drag urls, bookmarklets, files, <em>anything</em>.</p>
        <p>Check out the functionality in different browsers, because the same content appears differently when dropped. Something to watch out for in the future.</p>
        <p>Change the options below to see the difference between the default Text and sniffing for data (not supported in IE I'm afraid).</p>
        <p>Try also dropping a few files from your desktop on the drop zone and notice the content-type: text/uri-list</p>
        <div>
          <input name="getDataType" value="text" id="text" checked="checked" type="radio"> <label for="text">getData('Text')</label>
        </div>
        <div>
          <input name="getDataType" value="type" id="type" type="radio"> <label for="type">getData(e.dataTransfer.types[0]) based on detected content type</label>
        </div>
      </section>
      <section id="drag">
         <p><img class="photo" src="http://a3.twimg.com/profile_images/82806383/remysharp_normal.jpg" alt="Remy Sharp" style="float: left; margin: 3px 10px 10px 0pt;"> My name is <a class="fn n url" rel="me" href="http://remysharp.com">Remy Sharp</a> (<a href="http://twitter.com/rem">@rem on Twitter</a> and <a href="http://remysharp.com">my blog</a>).  <span class="adr">I run a small <abbr class="type" title="Work">business</abbr> in <a href="http://www.flickr.com/places/United+Kingdom/England/Brighton"><span class="region">Brighton</span>, <abbr class="country-name" title="United Kingdom">UK</abbr></a> called <a class="org url" rel="me" href="http://leftlogic.com">Left Logic</a> and am running the <a href="http://full-frontal.org">Full Frontal JavaScript Conference</a> and I specialise in <em>bespoke</em> front-end development & backend.</span></p>
      </section>
      <section id="drop">
        <p class="info">Drop here for info about the dragged item</p>
      </section>
    </article>
<script>

function cancel(e) {
  if (e.preventDefault) e.preventDefault(); // required by FF + Safari
//  e.dataTransfer.dropEffect = 'copy'; // tells the browser what drop effect is allowed here
  return false; // required by IE
}

function entities(s) {
  var e = {
    '"' : '"',
    '&' : '&',
    '<' : '<',
    '>' : '>'
  };
  return s.replace(/["&<>]/g, function (m) {
    return e[m];
  });
}

var getDataType = document.getElementById('text');
var drop = document.getElementById('drop');

// Tells the browser that we *can* drop on this target
addEvent(drop, 'dragover', cancel);
addEvent(drop, 'dragenter', cancel);

addEvent(drop, 'drop', function (e) {
  if (e.preventDefault) e.preventDefault(); // stops the browser from redirecting off to the text.

  // just rendering the text in to the list

  // clear out the original text
  drop.innerHTML = '<ul></ul>';
  
  var li = document.createElement('li');
  
  /** THIS IS THE MAGIC: we read from getData based on the content type - so it grabs the item matching that format **/
  if (getDataType.checked == false && e.dataTransfer.types) {
    li.innerHTML = '<ul>';
    [].forEach.call(e.dataTransfer.types, function (type) {
      li.innerHTML += '<li>' + entities(e.dataTransfer.getData(type) + ' (content-type: ' + type + ')') + '</li>';
    });
    li.innerHTML += '</ul>';
    
  } else {
    // ... however, if we're IE, we don't have the .types property, so we'll just get the Text value
    li.innerHTML = e.dataTransfer.getData('Text');
  }
  
  var ul = drop.getElementsByTagName('ul')[0];

  if (ul.firstChild) {
    ul.insertBefore(li, ul.firstChild);
  } else {
    ul.appendChild(li);
  }
  
  return false;
});
</script>

    <footer><a href="/">HTML5 demos</a>/<a id="built" href="http://twitter.com/rem">@rem built this</a>/<a href="#view-source">view source</a></footer> 
</section>
<a href="http://github.com/remy/html5demos"><img style="position: absolute; top: 0pt; left: 0pt; border: 0pt none;" src="http://s3.amazonaws.com/github/ribbons/forkme_left_darkblue_121621.png" alt="Fork me on GitHub"></a>
<script>
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script><script src="http://www.google-analytics.com/ga.js" type="text/javascript"></script>
<script>
try {
var pageTracker = _gat._getTracker("UA-1656750-18");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
</html>