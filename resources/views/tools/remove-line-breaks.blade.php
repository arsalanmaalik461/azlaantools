@extends('layouts.app')

@section('title', 'Remove Line Breaks Online - Fix Text Copied from PDF | Azlaan Tools')
@section('meta_description', 'Free tool to remove line breaks from text copied from PDFs and emails. Keep paragraph breaks, collapse extra spaces, and copy clean text instantly.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Remove Line Breaks</h1>
            <p class="lead text-muted">Clean up text copied from PDFs, emails and documents — remove unwanted line breaks in one click.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="input">Before — paste your text</label>
                    <textarea class="form-control" id="input" rows="7" placeholder="Paste text with messy line breaks here..."></textarea>
                    <div class="form-check mt-3"><input class="form-check-input" type="checkbox" id="keepPara" checked><label class="form-check-label" for="keepPara">Keep paragraph breaks (double line breaks stay as new paragraphs)</label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" id="collapse" checked><label class="form-check-label" for="collapse">Collapse multiple spaces into one</label></div>
                    <label class="form-label fw-semibold mt-3" for="output">After — clean text</label>
                    <textarea class="form-control" id="output" rows="7" readonly></textarea>
                    <div class="small text-muted mt-2" id="stats">0 characters before → 0 after · 0 line breaks removed</div>
                    <div class="d-flex gap-2 mt-3"><button type="button" class="btn btn-success btn-sm" id="copyBtn">Copy Clean Text</button><button type="button" class="btn btn-outline-danger btn-sm" id="clearBtn">Clear</button></div>
                </div>
            </div>
            <h2>How to use</h2>
            <ol><li>Paste your messy text in the top box.</li><li>Choose whether to keep paragraph breaks and collapse extra spaces.</li><li>The clean text appears instantly below — copy and use it anywhere.</li></ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function(){
 var input=document.getElementById('input'),output=document.getElementById('output');
 function update(){
  var t=input.value;
  var breaks=(t.match(/\n/g)||[]).length;
  var out;
  if(document.getElementById('keepPara').checked){
    out=t.split(/\n\s*\n/).map(function(p){return p.replace(/\s*\n\s*/g,' ');}).join('\n\n');
  } else {
    out=t.replace(/\s*\n\s*/g,' ');
  }
  if(document.getElementById('collapse').checked) out=out.replace(/[ \t]+/g,' ');
  out=out.trim();
  var removed=breaks-(out.match(/\n/g)||[]).length; if(removed<0)removed=0;
  output.value=out;
  document.getElementById('stats').textContent=t.length.toLocaleString()+' characters before → '+out.length.toLocaleString()+' after · '+removed+' line breaks removed';
 }
 input.addEventListener('input',update);
 document.getElementById('keepPara').addEventListener('change',update);
 document.getElementById('collapse').addEventListener('change',update);
 document.getElementById('copyBtn').addEventListener('click',function(){if(output.value&&navigator.clipboard)navigator.clipboard.writeText(output.value);});
 document.getElementById('clearBtn').addEventListener('click',function(){input.value='';update();input.focus();});
 update();
})();
</script>
@endsection
