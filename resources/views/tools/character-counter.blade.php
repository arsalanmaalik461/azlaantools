@extends('layouts.app')

@section('title', 'Character Counter - Live Character, Word & Letter Count | Azlaan Tools')
@section('meta_description', 'Free online character counter. Live counts for characters, letters, words, sentences and paragraphs, plus limit meters for X/Twitter, Instagram, SMS and meta tags.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Character Counter</h1>
            <p class="lead text-muted">Count characters, letters, words, sentences and paragraphs live — and check your text against popular limits.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="input">Your Text</label>
                    <textarea class="form-control" id="input" rows="8" placeholder="Start typing or paste your text here..."></textarea>
                    <div class="d-flex gap-2 mt-3 flex-wrap"><button type="button" class="btn btn-outline-secondary btn-sm" id="clearBtn">Clear</button><button type="button" class="btn btn-success btn-sm" id="copyBtn">Copy Text</button></div>
                    <div class="row g-3 text-center mt-2">
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="fs-4 fw-bold" id="cChars">0</div><div class="text-muted small">Characters</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="fs-4 fw-bold" id="cNoSpace">0</div><div class="text-muted small">Without Spaces</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="fs-4 fw-bold" id="cLetters">0</div><div class="text-muted small">Letters Only</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="fs-4 fw-bold" id="cWords">0</div><div class="text-muted small">Words</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="fs-4 fw-bold" id="cSent">0</div><div class="text-muted small">Sentences</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="fs-4 fw-bold" id="cPara">0</div><div class="text-muted small">Paragraphs</div></div></div>
                    </div>
                    <h3 class="h5 mt-4">Limit Meters</h3>
                    <div id="meters"></div>
                </div>
            </div>
            <h2>How to use</h2>
            <ol><li>Type or paste text in the box — every count updates instantly.</li><li>Check the meters below: green is safe, red means you are over that platform limit.</li><li>Use Copy Text to copy your text when it fits.</li></ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function(){
 var input=document.getElementById('input');
 var limits=[{name:'X / Twitter post',max:280},{name:'Instagram bio',max:150},{name:'SMS message',max:160},{name:'Meta title',max:60},{name:'Meta description',max:160}];
 var meters=document.getElementById('meters');
 limits.forEach(function(l,i){
   var d=document.createElement('div'); d.className='mb-3';
   d.innerHTML='<div class="d-flex justify-content-between small"><span>'+l.name+'</span><span id="mTxt'+i+'">0 / '+l.max+'</span></div><div class="progress" style="height:10px"><div class="progress-bar bg-success" id="mBar'+i+'" style="width:0%"></div></div>';
   meters.appendChild(d);
 });
 function update(){
  var t=input.value, trim=t.trim();
  var words=trim?trim.split(/\s+/).filter(Boolean):[];
  var sentences=0; if(trim){var m=trim.match(/[^.!?]+[.!?]+/g); sentences=m?m.length:1;}
  var paras=trim?trim.split(/\n\s*\n/).filter(function(p){return p.trim();}).length:0; if(trim&&paras===0)paras=1;
  document.getElementById('cChars').textContent=t.length.toLocaleString();
  document.getElementById('cNoSpace').textContent=t.replace(/\s/g,'').length.toLocaleString();
  document.getElementById('cLetters').textContent=(t.match(/[A-Za-z\u0600-\u06FF]/g)||[]).length.toLocaleString();
  document.getElementById('cWords').textContent=words.length.toLocaleString();
  document.getElementById('cSent').textContent=sentences;
  document.getElementById('cPara').textContent=paras;
  limits.forEach(function(l,i){
    var pct=Math.min(100, t.length/l.max*100);
    var bar=document.getElementById('mBar'+i); bar.style.width=pct+'%'; bar.className='progress-bar '+(t.length>l.max?'bg-danger':(pct>85?'bg-warning':'bg-success'));
    document.getElementById('mTxt'+i).textContent=t.length+' / '+l.max+(t.length>l.max?' ('+(t.length-l.max)+' over)':'');
  });
 }
 input.addEventListener('input',update);
 document.getElementById('clearBtn').addEventListener('click',function(){input.value='';update();input.focus();});
 document.getElementById('copyBtn').addEventListener('click',function(){if(input.value&&navigator.clipboard)navigator.clipboard.writeText(input.value);});
 update();
})();
</script>
@endsection
