@extends('layouts.app')

@section('title', 'Fancy Text Generator - Stylish Unicode Fonts | Azlaan Tools')
@section('meta_description', 'Free fancy text generator. Turn your text into bold, script, fraktur, circled, upside-down and many more stylish Unicode fonts. Click any style to copy.')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <h1 class="mb-3">Fancy Text Generator</h1>
            <p class="lead text-muted">Type your text and get stylish Unicode versions for bios, usernames and posts. Click any style to copy it.</p>
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold" for="input">Your Text</label>
                    <input class="form-control form-control-lg" id="input" type="text" placeholder="Type something, e.g. Azlaan" maxlength="120">
                    <div class="alert alert-success mt-3 d-none" id="msg">Copied!</div>
                    <div id="results" class="mt-3"></div>
                </div>
            </div>
            <h2>How to use</h2>
            <ol><li>Type your text in the box above.</li><li>All styles appear instantly below.</li><li>Click any styled row to copy it, then paste it into Instagram, WhatsApp, TikTok or anywhere else.</li></ol>
            <p class="text-muted small">Note: these are Unicode characters, not real fonts, so they work almost everywhere — but a few very old devices may show boxes for some styles.</p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
(function(){
 var input=document.getElementById('input'), results=document.getElementById('results'), msg=document.getElementById('msg');
 var lower='abcdefghijklmnopqrstuvwxyz', upper='ABCDEFGHIJKLMNOPQRSTUVWXYZ', digits='0123456789';
 function mapFrom(startLower,startUpper,startDigit){
   return function(ch){
     var li=lower.indexOf(ch), ui=upper.indexOf(ch), di=digits.indexOf(ch);
     if(li>=0) return String.fromCodePoint(startLower+li);
     if(ui>=0) return String.fromCodePoint(startUpper+ui);
     if(di>=0 && startDigit!==null) return String.fromCodePoint(startDigit+di);
     return ch;
   };
 }
 function customMap(lowerStr,upperStr,digitStr){
   var ls=Array.from(lowerStr||''), us=Array.from(upperStr||''), ds=Array.from(digitStr||'');
   return function(ch){
     var li=lower.indexOf(ch), ui=upper.indexOf(ch), di=digits.indexOf(ch);
     if(li>=0 && ls[li]) return ls[li];
     if(ui>=0 && us[ui]) return us[ui];
     if(di>=0 && ds[di]) return ds[di];
     return ch;
   };
 }
 var circledDigits='⓪①②③④⑤⑥⑦⑧⑨';
 var styles=[
  {name:'Bold', fn:mapFrom(0x1D41A,0x1D400,0x1D7CE)},
  {name:'Italic', fn:mapFrom(0x1D44E,0x1D434,null)},
  {name:'Bold Italic', fn:mapFrom(0x1D482,0x1D468,null)},
  {name:'Script', fn:customMap('𝓪𝓫𝓬𝓭𝓮𝓯𝓰𝓱𝓲𝓳𝓴𝓵𝓶𝓷𝓸𝓹𝓺𝓻𝓼𝓽𝓾𝓿𝔀𝔁𝔂𝔃','𝒜ℬ𝒞𝒟ℰℱ𝒢ℋℐ𝒥𝒦ℒℳ𝒩𝒪𝒫𝒬ℛ𝒮𝒯𝒰𝒱𝒲𝒳𝒴𝒵','')},
  {name:'Fraktur', fn:mapFrom(0x1D51E,0x1D504,null)},
  {name:'Double-Struck', fn:mapFrom(0x1D552,0x1D538,0x1D7D8)},
  {name:'Monospace', fn:mapFrom(0x1D68A,0x1D670,0x1D7F6)},
  {name:'Sans Bold', fn:mapFrom(0x1D5EE,0x1D5D4,0x1D7EC)},
  {name:'Small Caps', fn:customMap('ᴀʙᴄᴅᴇꜰɢʜɪᴊᴋʟᴍɴᴏᴘǫʀꜱᴛᴜᴠᴡxʏᴢ','ᴀʙᴄᴅᴇꜰɢʜɪᴊᴋʟᴍɴᴏᴘǫʀꜱᴛᴜᴠᴡxʏᴢ','')},
  {name:'Superscript', fn:customMap('ᵃᵇᶜᵈᵉᶠᵍʰⁱʲᵏˡᵐⁿᵒᵖqʳˢᵗᵘᵛʷˣʸᶻ','ᴬᴮᶜᴰᴱᶠᴳᴴᴵᴶᴷᴸᴹᴺᴼᴾQᴿˢᵀᵁⱽᵂˣʸᶻ','⁰¹²³⁴⁵⁶⁷⁸⁹')},
  {name:'Circled', fn:customMap('ⓐⓑⓒⓓⓔⓕⓖⓗⓘⓙⓚⓛⓜⓝⓞⓟⓠⓡⓢⓣⓤⓥⓦⓧⓨⓩ','ⒶⒷⒸⒹⒺⒻⒼⒽⒾⒿⓀⓁⓂⓃⓄⓅⓆⓇⓈⓉⓊⓋⓌⓍⓎⓏ',circledDigits)},
  {name:'Squared', fn:customMap('🄰🄱🄲🄳🄴🄵🄶🄷🄸🄹🄺🄻🄼🄽🄾🄿🅀🅁🅂🅃🅄🅅🅆🅇🅈🅉','🄰🄱🄲🄳🄴🄵🄶🄷🄸🄹🄺🄻🄼🄽🄾🄿🅀🅁🅂🅃🅄🅅🅆🅇🅈🅉','')},
  {name:'Wide / Fullwidth', fn:function(ch){ if(ch===' ')return '　'; var c=ch.charCodeAt(0); if(c>=33&&c<=126) return String.fromCharCode(c+65248); return ch; } },
  {name:'Upside Down', fn:null},
  {name:'Zalgo (light)', fn:null}
 ];
 var flipMap={a:'ɐ',b:'q',c:'ɔ',d:'p',e:'ǝ',f:'ɟ',g:'ƃ',h:'ɥ',i:'ᴉ',j:'ɾ',k:'ʞ',l:'l',m:'ɯ',n:'u',o:'o',p:'d',q:'b',r:'ɹ',s:'s',t:'ʇ',u:'n',v:'ʌ',w:'ʍ',x:'x',y:'ʎ',z:'z',A:'∀',B:'q',C:'Ɔ',D:'p',E:'Ǝ',F:'Ⅎ',G:'⅁',H:'H',I:'I',J:'ſ',K:'ʞ',L:'˥',M:'W',N:'N',O:'O',P:'d',Q:'b',R:'ɹ',S:'S',T:'⊥',U:'∩',V:'Λ',W:'M',X:'X',Y:'⅄',Z:'Z','0':'0','1':'Ɩ','2':'ᄅ','3':'Ɛ','4':'ㄣ','5':'ϛ','6':'9','7':'ㄥ','8':'8','9':'6','.':'˙',',':"'",'?':'¿','!':'¡','(' :')',')':'('};
 function flip(t){ return t.split('').reverse().map(function(ch){return flipMap[ch]||ch;}).join(''); }
 var zalgoMarks=['\u0301','\u0300','\u0302','\u0303','\u0304','\u0308'];
 function zalgo(t){ return t.split('').map(function(ch,i){ if(ch===' ')return ch; return ch+zalgoMarks[i%zalgoMarks.length]; }).join(''); }
 function render(){
   var t=input.value||'Azlaan';
   results.innerHTML='';
   styles.forEach(function(s){
     var out;
     if(s.name==='Upside Down') out=flip(t);
     else if(s.name.indexOf('Zalgo')===0) out=zalgo(t);
     else out=t.split('').map(s.fn).join('');
     var btn=document.createElement('button'); btn.type='button'; btn.className='btn btn-outline-dark w-100 text-start mb-2 py-2';
     btn.innerHTML=''; var small=document.createElement('span'); small.className='text-muted small d-block'; small.textContent=s.name;
     var span=document.createElement('span'); span.className='fs-5'; span.textContent=out;
     btn.appendChild(small); btn.appendChild(span);
     btn.addEventListener('click',function(){
       if(navigator.clipboard) navigator.clipboard.writeText(out);
       msg.classList.remove('d-none'); setTimeout(function(){msg.classList.add('d-none');},1500);
     });
     results.appendChild(btn);
   });
 }
 input.addEventListener('input',render); render();
})();
</script>
@endsection
