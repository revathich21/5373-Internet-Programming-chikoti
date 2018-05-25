
grabProducts($('#candy-content'),'all'); //attach_cart_events


function grabProducts($dom_ele,category) {//callback
    //alert('hi');
    // console.log(category);
    $dom_ele.html("");

   // offset = (typeof offset !== 'undefined') ? offset : 0;
 //   size = (typeof size !== 'undefined') ? size : 10;
    category = (typeof category !== 'undefined') ? category : 'all';

    var route = "browse";// + "/category/" + category;
   // console.log('hi');

    $.get(route)
        .done(function (data) {
            var obj = JSON.parse(data);
            var length = obj['data'].length;
            for(var i=0;i<length;i++){
                $dom_ele.append(build_product_card(obj['data'][i]));
            }
            //callback();
        });
}

function build_product_card(data) {
    var img = '/chocolate/small_images/'+data.image_path+'_small'+'.'+data.img_type;
    var short = data.description.substr(0,100)+"...";
    var html = '';
    html += '<div class="col-lg-4 col-md-6 mb-4">';
    html += ' <div class="card h-100">';
    html += '  <a href="#"><img class="card-img-top" src="'+img+'" alt=""></a>';
    html += '   <div class="card-body">';
    html += '    <h4 class="card-title">';
    html += '      <a href="#">'+data.name+'</a>';
    html += '    <span><i class="fas fa-shopping-cart addcart my-cart-btn" data-pid="'+data.id+'" style="font-size:14px"></i></span>';
    html += '    </h4>';
    html += '    <h5>$'+data.price+'</h5>';
    html += '    <p class="card-text">'+short+'</p>';
    html += '   </div>';
    //html += '   <div class="card-footer">';
    //html += '   <small class="text-muted">&#9733; &#9733; &#9733; &#9733; &#9734;</small>';
    //html += '  </div>';
    html += ' </div>';
    html += '</div>';
    return html;
}



function attach_cart_events(){

    var guid = getCookie("candy_store");

    //console.log("adding cart events");
    $(".addcart").click(function(e){
        // console.log($( this ).data());
        // console.log(e);
        var data = $( this ).data();
        //console.log(data.pid);
        //console.log(guid);
        var route = "app.php/addCart/pid/" + data.pid + "/uid/"+guid;

        $.get(route)
            .done(function (data) {
                //console.log(data);
                update_cart_badge();
            });
    });
}

$("#view-cart").click(function(e){
    //console.log($( this ).data());
    load_cart_modal();
});

function load_cart_modal(){
    var guid = getCookie("candy_store");
    var route = "app.php/getCart/uid/"+guid;
    var html = "";
    $("#cartTable").html("");
    $.get(route)
        .done(function (data) {
            var data = data.data;
            //console.log(data);
            for(i=0;i<data.length;i++){
                html += "<tr>";
                html += "<td><img src=\""+data[i].image_path+"_small."+data[i].img_type+"\" height='50'></td><td>"+data[i].name+"</td><td>"+data[i].pcount+"</td><td>"+data[i].price+"</td><td><a href=\"#\" data-id=\""+data[i].id+"\" class=\"delete-item\"><i class=\"fas fa-trash-alt\" style=\"color:red;text-align:center\"></i></a></td>";
                html += "</tr>";
            }
            $("#cartTable").append(html);
            attach_delete_event();
           
            $(".close").click(function(e){
                update_cart_badge()
            });
        });
}


function attach_delete_event(){

    var guid = getCookie("candy_store");

    $(".delete-item").click(function(e){
        //console.log($( this ).data());
        //console.log(guid);

        var data = $( this ).data();

        var pid = data.id;

        var route = "app.php/deleteCart/uid/"+guid+"/pid/"+pid;

        $.get(route)
        .done(function (data) {
            //console.log(data);
            load_cart_modal();
        });
    });
}

function update_cart_badge(){
    var guid = getCookie("candy_store");
    var route = "app.php/getCartSize/uid/"+guid;

    $.get(route)
    .done(function (data) {
        var count = data.data.size;
        if(count > 0){
            //console.log($('#cart-badge').html());
            $('#cart-badge').html(count);
            $('#cart-badge').show();
        }else{
            $('#cart-badge').hide();
        }
    });
}

$('.catclass').click(function(){
    console.log('pfpf');
  });