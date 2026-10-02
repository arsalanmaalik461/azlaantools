@extends('layouts.app')

@section('title', 'Text Repeater - Repeat Text 1000 Times | Azlaan Tools')
@section('meta_description', 'Free text repeater. Repeat any word, sentence or emoji up to 10,000 times with space, newline or comma separators. Copy or download instantly.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Text Repeater</h1>
            <p class="lead text-muted">Repeat any text as many times as you need — perfect for messages, lists and fun posts.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="input">Text to repeat</label>
                    <input class="form-control" id="input" type="text" placeholder="e.g. I love Pakistan" value="">
                    <div class="row g-3 mt-1">
                        <div class="col-6"><label class="form-label fw-semibold" for="count">Repeat times (1–10,000)</label><input class="form-control" id="count" type="number" min="1" max="10000" value="10"></div>
                        <div class="col-6"><label class="form-label fw-semibold" for="sep">Separator</label><select class="form-select" id="sep"><option value=" ">Space</option><option value="\n">New line</option><option value=", ">Comma</option><option value="">Nothing</option></select></div>
                    </div>
                    <div class="d-flex gap-2 mt-3 flex-wrap"><button type="button" class="btn btn-success btn-sm" id="copyBtn">Copy Output</button><button type="button" class="btn btn-outline-success btn-sm" id="dlBtn">Download .txt</button></div>
                    <label class="form-label fw-semibold mt-3" for="output">Output <span class="text-muted fw-normal" id="outCount">(0 characters)</span></label>
                    <textarea class="form-control" id="output" rows="8" readonly></textarea>
                </div>
            </div>
            <h2>How to use</h2>
            <ol><li>Type the text you want to repeat.</li><li>Set how many times (up to 10,000) and pick a separator.</li><li>The output updates instantly — copy it or download it as a .txt file.</li></ol>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function(){
 var input=document.getElementById('input'),count=document.getElementById('count'),sep=document.getElementById('sep'),output=document.getElementById('output');
 function update(){
   var n=parseInt(count.value,10); if(isNaN(n))n=0; if(n<0)n=0; if(n>10000){n=10000;count.value=10000;}
   var s=sep.value==='\\n'?'\n':sep.value;
   var parts=[]; for(var i=0;i<n;i++) parts.push(input.value);
   var out=input.value?parts.join(s):'';
   output.value=out;
   document.getElementById('outCount').textContent='('+out.length.toLocaleString()+' characters)';
 }
 [input,count,sep].forEach(function(el){el.addEventListener('input',update);el.addEventListener('change',update);});
 document.getElementById('copyBtn').addEventListener('click',function(){if(output.value&&navigator.clipboard)navigator.clipboard.writeText(output.value);});
 document.getElementById('dlBtn').addEventListener('click',function(){
   var blob=new Blob([output.value],{type:'text/plain;charset=utf-8'}); var url=URL.createObjectURL(blob); var a=document.createElement('a'); a.href=url; a.download='repeated-text.txt'; document.body.appendChild(a); a.click(); a.remove(); setTimeout(function(){URL.revokeObjectURL(url);},2000);
 });
 update();
})();
</script>
@endsection
