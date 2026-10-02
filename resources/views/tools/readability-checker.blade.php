@extends('layouts.app')

@section('title', 'Readability Checker - Flesch Reading Ease & Grade Level | Azlaan Tools')
@section('meta_description', 'Free readability checker for English text. Get Flesch Reading Ease, Flesch-Kincaid Grade and Gunning Fog scores with a plain-English verdict, instantly.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Readability Checker</h1>
            <p class="lead text-muted">Check how easy your writing is to read. <span class="badge bg-secondary">For English text</span></p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="input">Your English Text</label>
                    <textarea class="form-control" id="input" rows="8" placeholder="Paste at least a few sentences for a reliable score..."></textarea>
                    <div class="text-center my-3"><span class="badge fs-5" id="verdict">Start typing</span></div>
                    <div class="row g-3 text-center">
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="fs-4 fw-bold" id="rEase">–</div><div class="text-muted small">Flesch Reading Ease (0–100, higher = easier)</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="fs-4 fw-bold" id="rGrade">–</div><div class="text-muted small">Flesch-Kincaid Grade</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="fs-4 fw-bold" id="rFog">–</div><div class="text-muted small">Gunning Fog Index</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="fs-5 fw-bold" id="rWords">0</div><div class="text-muted small">Words</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="fs-5 fw-bold" id="rAvg">0</div><div class="text-muted small">Avg. Words / Sentence</div></div></div>
                        <div class="col-6 col-md-4"><div class="border rounded p-3"><div class="fs-5 fw-bold" id="rSyll">0</div><div class="text-muted small">Avg. Syllables / Word</div></div></div>
                    </div>
                    <p class="small text-muted mt-3 mb-0">Scores are estimates based on sentence length and a syllable-counting heuristic. Short texts give rough results.</p>
                </div>
            </div>
            <h2>How to use</h2>
            <ol><li>Paste English text (a few sentences or more works best).</li><li>Read the verdict badge and scores — they update live.</li><li>Aim for Reading Ease 60–70 and shorter sentences for general readers.</li></ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function(){
 var input=document.getElementById('input');
 function syllables(word){
   var w=word.toLowerCase().replace(/[^a-z]/g,''); if(!w) return 0; if(w.length<=3) return 1;
   var v=w.replace(/e$/,'').match(/[aeiouy]+/g); var n=v?v.length:1;
   if(/[^aeiou]le$/.test(w)) n++;
   return Math.max(1,n);
 }
 function update(){
  var t=input.value.trim();
  var words=t?t.split(/\s+/).filter(Boolean):[];
  var sentences=0; if(t){var m=t.match(/[^.!?]+[.!?]+/g); sentences=m?m.length:1;}
  var totalSyll=0, complex=0;
  words.forEach(function(w){var s=syllables(w); totalSyll+=s; if(s>=3) complex++;});
  var verdict=document.getElementById('verdict');
  if(words.length<3||sentences===0){
    ['rEase','rGrade','rFog'].forEach(function(id){document.getElementById(id).textContent='–';});
    verdict.textContent='Add more text'; verdict.className='badge fs-5 bg-secondary';
    document.getElementById('rWords').textContent=words.length; document.getElementById('rAvg').textContent='0'; document.getElementById('rSyll').textContent='0'; return;
  }
  var asl=words.length/sentences, asw=totalSyll/words.length;
  var ease=206.835-(1.015*asl)-(84.6*asw); ease=Math.max(0,Math.min(100,ease));
  var grade=(0.39*asl)+(11.8*asw)-15.59;
  var fog=0.4*(asl+100*(complex/words.length));
  document.getElementById('rEase').textContent=ease.toFixed(0);
  document.getElementById('rGrade').textContent=Math.max(0,grade).toFixed(1);
  document.getElementById('rFog').textContent=Math.max(0,fog).toFixed(1);
  document.getElementById('rWords').textContent=words.length;
  document.getElementById('rAvg').textContent=asl.toFixed(1);
  document.getElementById('rSyll').textContent=asw.toFixed(2);
  var label,cls;
  if(ease>=80){label='Very easy to read';cls='bg-success';}
  else if(ease>=60){label='Easy — good for everyone';cls='bg-success';}
  else if(ease>=50){label='Fairly difficult — simplify a little';cls='bg-warning text-dark';}
  else if(ease>=30){label='Difficult — try shorter sentences';cls='bg-warning text-dark';}
  else {label='Very difficult — rewrite in simpler words';cls='bg-danger';}
  verdict.textContent=label; verdict.className='badge fs-5 '+cls;
 }
 input.addEventListener('input',update); update();
})();
</script>
@endsection
