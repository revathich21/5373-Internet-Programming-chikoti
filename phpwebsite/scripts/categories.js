

loadCategoryMenu($('#candy-categories'));


function loadCategoryMenu($dom_ele) {

    $dom_ele.html("");

    //javascript default params if they dont exist
    offset = (typeof offset !== 'undefined') ? offset : 0;
    size = (typeof size !== 'undefined') ? size : 10;

    var route = "categories";

    $.get(route)
        .done(function (data) {
            var obj = JSON.parse(data);
            var length = obj['data'].length;
          // data = data.data;
        //  console.log(typeof data);
            for(var i=0;i<length;i++){
               // console.log(data[i]);
               var category =  obj['data'][i].category;
               $dom_ele.append('<a href="#'+category+'" '+'class="list-group-item catclass" data-category="'+ obj['data'][i].category+'">'+obj['data'][i].category+'</a>');
            }


        });

        
        $('.catclass').click(function(){
            console.log('pfpf');
          });

        
}
$('.catclass').click(function(){
    console.log('pfpf');
  });
