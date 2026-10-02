@extends('layouts.app')

@section('title', 'Text to Handwriting Converter - Handwritten Notes PNG | Azlaan Tools')
@section('meta_description', 'Free text to handwriting converter. Turn typed text into realistic handwritten pages with pen colour choices and download as PNG images.')

@section('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Caveat:wght@500;700&family=Shadows+Into+Light&family=Kalam:wght@400;700&display=swap" rel="stylesheet">
<style>
@media print { body * { visibility: hidden; } }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Text to Handwriting Converter</h1>
            <p class="lead text-muted">Type your text and turn it into a handwritten page you can download as a PNG image.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="input">Your Text</label>
                    <textarea class="form-control" id="input" rows="6" placeholder="Type your assignment, notes or letter here...">Dear Teacher,
This is my homework written by hand.
Thank you!</textarea>
                    <div class="row g-3 mt-1">
                        <div class="col-6"><label class="form-label" for="fontSel">Handwriting style</label><select class="form-select" id="fontSel"><option value="Caveat">Caveat (neat cursive)</option><option value="Shadows Into Light">Shadows Into Light (casual)</option><option value="Kalam">Kalam (marker style)</option></select></div>
                        <div class="col-6"><label class="form-label" for="inkSel">Ink colour</label><select class="form-select" id="inkSel"><option value="#1a3a8f">Blue pen</option><option value="#111111">Black pen</option><option value="#b00020">Red pen</option></select></div>
                    </div>
                    <div class="d-flex gap-2 mt-3 flex-wrap"><button type="button" class="btn btn-primary btn-sm" id="renderBtn">Make Handwriting Page</button><button type="button" class="btn btn-success btn-sm" id="dlBtn">Download PNG (all pages)</button></div>
                    <p class="small text-muted mt-2" id="pageInfo">Press the button to render. Long text automatically continues on extra pages (up to 3).</p>
                    <div id="pages" class="mt-3"></div>
                </div>
            </div>
            <h2>How to use</h2>
            <ol><li>Type or paste your text.</li><li>Pick a handwriting style and ink colour, then click Make Handwriting Page.</li><li>Preview the page(s) and download them as PNG images.</li></ol>
            <p class="text-muted small">Tip: this uses real handwriting-style fonts, so it looks like neat handwriting — not a scan of your own hand.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function(){
 var W=794, H=1123, MARGIN=70, LINE=42, FONT_SIZE=30;
 var canvases=[];
 function wrapLines(ctx, text){
   var maxW=W-(MARGIN*2), lines=[];
   text.split('\n').forEach(function(para){
     if(para.trim()===''){ lines.push(''); return; }
     var words=para.split(/\s+/), line='';
     words.forEach(function(w){
       var test=line?line+' '+w:w;
       if(ctx.measureText(test).width>maxW && line){ lines.push(line); line=w; } else { line=test; }
     });
     if(line) lines.push(line);
   });
   return lines;
 }
 function render(){
   var text=document.getElementById('input').value;
   var font=document.getElementById('fontSel').value;
   var ink=document.getElementById('inkSel').value;
   var holder=document.getElementById('pages'); holder.innerHTML=''; canvases=[];
   var meas=document.createElement('canvas').getContext('2d'); meas.font=FONT_SIZE+'px "'+font+'", cursive';
   var lines=wrapLines(meas, text);
   var perPage=Math.floor((H-(MARGIN*2))/LINE);
   var pages=[]; for(var i=0;i<lines.length;i+=perPage) pages.push(lines.slice(i,i+perPage));
   if(pages.length===0) pages=[[]];
   if(pages.length>3){ pages=pages.slice(0,3); document.getElementById('pageInfo').textContent='Text was long, so it was trimmed to 3 pages. Shorten the text or split it into parts for more pages.'; }
   else { document.getElementById('pageInfo').textContent=pages.length+' page(s) ready.'; }
   function draw(){
     pages.forEach(function(pageLines,pi){
       var cv=document.createElement('canvas'); cv.width=W; cv.height=H; cv.className='img-fluid border shadow-sm mb-3 w-100';
       var ctx=cv.getContext('2d');
       ctx.fillStyle='#fffef7'; ctx.fillRect(0,0,W,H);
       ctx.strokeStyle='#f3b8c6'; ctx.lineWidth=2; ctx.beginPath(); ctx.moveTo(MARGIN-18,0); ctx.lineTo(MARGIN-18,H); ctx.stroke();
       ctx.strokeStyle='#bcd6f5'; ctx.lineWidth=1;
       for(var y=MARGIN+LINE-10; y<H-MARGIN; y+=LINE){ ctx.beginPath(); ctx.moveTo(0,y); ctx.lineTo(W,y); ctx.stroke(); }
       ctx.fillStyle=ink; ctx.font=FONT_SIZE+'px "'+font+'", cursive'; ctx.textBaseline='alphabetic';
       pageLines.forEach(function(line,li){ ctx.fillText(line, MARGIN, MARGIN+ (li+1)*LINE - 14); });
       ctx.fillStyle='#999'; ctx.font='14px sans-serif'; ctx.fillText('Page '+(pi+1)+' of '+pages.length, W-120, H-24);
       holder.appendChild(cv); canvases.push(cv);
     });
   }
   if(document.fonts && document.fonts.ready){ document.fonts.ready.then(function(){ setTimeout(draw, 350); }); } else { draw(); }
 }
 document.getElementById('renderBtn').addEventListener('click', render);
 document.getElementById('dlBtn').addEventListener('click', function(){
   if(!canvases.length) render();
   setTimeout(function(){
     canvases.forEach(function(cv,i){
       var a=document.createElement('a'); a.href=cv.toDataURL('image/png'); a.download='handwriting-page-'+(i+1)+'.png'; document.body.appendChild(a); a.click(); a.remove();
     });
   }, canvases.length?50:600);
 });
 render();
})();
</script>
@endsection
