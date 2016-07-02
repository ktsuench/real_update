function text_counter(e1,e2,n){
    var text_max=n;
    e2.innerHTML = text_max + ' characters remaining';
    e2.classList.add('text-primary');

    e1.onkeyup = function(){
        var text_length = e1.value.length,
            text_remaining = text_max - text_length;

        e2.innerHTML = (text_remaining < 0 ? 0 : text_remaining) + ' characters remaining';
        
        if(text_remaining <= 0){
            e2.classList.remove('text-primary');
            e2.classList.add('text-danger');
        }else if(e2.classList.contains('text-danger')){
            e2.classList.remove('text-danger');
            e2.classList.add('text-primary');
        }
    };
}