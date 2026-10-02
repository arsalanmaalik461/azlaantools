@extends('layouts.app')

@section('title', 'Word Frequency Counter - Word & Phrase Density Checker | Azlaan Tools')
@section('meta_description', 'Free word frequency counter. See ranked words with counts and density percentage, plus top 2-word and 3-word phrases. Ignore common stop words with one click.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Word Frequency Counter</h1>
            <p class="lead text-muted">Find which words and phrases you use most — with counts and density percentages.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="input">Your Text</label>
                    <textarea class="form-control" id="input" rows="8" placeholder="Paste your article, essay or post here..."></textarea>
                    <div class="row g-3 mt-1 align-items-end">
                        <div class="col-6"><label class="form-label" for="minLen">Minimum word length</label><input class="form-control" id="minLen" type="number" min="1" max="10" value="1"></div>
                        <div class="col-6"><div class="form-check"><input class="form-check-input" type="checkbox" id="stopWords" checked><label class="form-check-label" for="stopWords">Ignore common stop words</label></div></div>
                    </div>
                    <p class="small text-muted mt-2" id="summary">No text yet.</p>
                    <h3 class="h5 mt-3">Single Words</h3>
                    <div class="table-responsive"><table class="table table-sm table-striped"><thead><tr><th>#</th><th>Word</th><th>Count</th><th>Density %</th></tr></thead><tbody id="wordBody"><tr><td colspan="4" class="text-muted">Type to see results.</td></tr></tbody></table></div>
                    <h3 class="h5 mt-3">2-Word Phrases</h3>
                    <div id="phrases2" class="small">—</div>
                    <h3 class="h5 mt-3">3-Word Phrases</h3>
                    <div id="phrases3" class="small">—</div>
                </div>
            </div>
            <h2>How to use</h2>
            <ol><li>Paste your text — results update instantly.</li><li>Use minimum length and the stop-words toggle to hide words like “the” and “and”.</li><li>Check phrase lists to spot repeated wording you may want to vary.</li></ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function(){
 var input=document.getElementById('input');
 var stopEn='the be to of and a in that it is was for on are as with his they i at be this have from or one had by word but not what all were we when your can said there use an each which she do how their if will up other about out many then them these so some her would make like him into time has look two more write go see number no way could people my than first water been call who oil its now find long down day did get come made may part'.split(' ');
 var stopUrdu=['کا','کی','کے','کو','میں','نے','ہے','ہیں','اور','سے','پر','بھی','یہ','وہ','ایک','نہیں','تھا','تھی','تھے','ہو','گا','گی','گے','کر','کے','لیے','بعد','جب','تو','ہی','اب','جو','اس','ان','کیا','کیوں','کیسے','کہ','تک','بارے','والا','والی','والے','ہونا','ہونے','رہا','رہی','رہے','دیا','دی','گئی','گیا','گئے','چاہیے','میرا','میری','میرے','تم','آپ','ہم','وہی','یہی','کوئی','کچھ','بہت','پھر','لیکن','اگر','مگر','یا','نہ','نا','ذرا','ابھی','کبھی','ہر','اسے','انہیں','مجھے','تمہیں','ہمیں','انہیں','جس','جن','تن','اس','اُس'];
 var stopSet={}; stopEn.forEach(function(w){stopSet[w]=1;}); stopUrdu.forEach(function(w){stopSet[w]=1;});
 function tokenise(t){ return t.toLowerCase().replace(/[^\p{L}\p{N}\s'-]/gu,' ').split(/\s+/).filter(Boolean); }
 function topPhrases(words,n,limit){
   var freq={}; for(var i=0;i+n<=words.length;i++){ var p=words.slice(i,i+n).join(' '); freq[p]=(freq[p]||0)+1; }
   return Object.keys(freq).map(function(k){return {k:k,c:freq[k]};}).filter(function(e){return e.c>1;}).sort(function(a,b){return b.c-a.c;}).slice(0,limit);
 }
 function renderPhrases(id,words,n){
   var list=topPhrases(words,n,10); var el=document.getElementById(id);
   el.textContent=list.length?list.map(function(e){return e.k+' ('+e.c+')';}).join(' · '):'No repeated phrases found.';
 }
 function update(){
  var words=tokenise(input.value);
  var minLen=parseInt(document.getElementById('minLen').value,10)||1;
  var useStop=document.getElementById('stopWords').checked;
  var filtered=words.filter(function(w){ return w.length>=minLen && !(useStop&&stopSet[w]); });
  document.getElementById('summary').textContent=words.length.toLocaleString()+' total words · '+filtered.length.toLocaleString()+' counted after filters.';
  var freq={}; filtered.forEach(function(w){freq[w]=(freq[w]||0)+1;});
  var entries=Object.keys(freq).map(function(k){return {k:k,c:freq[k]};}).sort(function(a,b){return b.c-a.c||a.k.localeCompare(b.k);}).slice(0,100);
  var body=document.getElementById('wordBody'); body.innerHTML='';
  if(!entries.length){ body.innerHTML='<tr><td colspan="4" class="text-muted">No words to show.</td></tr>'; }
  entries.forEach(function(e,i){
    var tr=document.createElement('tr');
    [String(i+1),e.k,String(e.c), filtered.length?((e.c/filtered.length)*100).toFixed(2):'0'].forEach(function(v){var td=document.createElement('td');td.textContent=v;tr.appendChild(td);});
    body.appendChild(tr);
  });
  renderPhrases('phrases2',words,2); renderPhrases('phrases3',words,3);
 }
 input.addEventListener('input',update);
 document.getElementById('minLen').addEventListener('input',update);
 document.getElementById('stopWords').addEventListener('change',update);
 update();
})();
</script>
@endsection
