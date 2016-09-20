$(document).ready(function(){
    google.charts.load('current', {packages:['wordtree']});
    
    var text = $('#text').val();
    $('.h').text('');   
    $('.l').text('');     
    $('.table table').remove();    
    $('#wordtree_explicit div').remove();    
    get_tree(text);
    
    $('#send').click(function(){
        var text = $('#text').val();
        $('.h').text('');   
        $('.l').text('');     
        $('.table table').remove();    
        $('#wordtree_explicit div').remove();    
        get_tree(text);
    });

    function get_tree(text){
        $.ajax({
        type:"POST",
        url:"/huff.php",
        data: {
            text: text
        },  
        dataType:"json",
            success: function(data1){  

                google.charts.setOnLoadCallback(drawSimpleNodeChart);
                function drawSimpleNodeChart() {
                  var nodeListData = new google.visualization.arrayToDataTable([
                    ['id', 'childLabel', 'parent', 'size', { role: 'style' }],
                    [0, '12', -1, 12, 'black'],
                    [1, '12', -1, 12, 'black'],
                    [2, '12', 0, 10, 'black']
                    ]);

                 var data = new google.visualization.DataTable();  

                 data.addColumn('number', 'id');
                 data.addColumn('string', 'childLabel');
                 data.addColumn('number', 'parent');
                 data.addColumn('number', 'size');
                 data.addColumn('string', { role: 'style' });

                for(var index in data1[0]) { 
                    console.log(); 
                    var weight = '';
                    if(data1[0][index]['weight']!=null){
                        weight = '('+data1[0][index]['weight']+')' + data1[0][index]['name'].toString();
                    }else{
                        weight = data1[0][index]['name'].toString();
                    }
                    data.addRow([Number(data1[0][index]['index']), weight, Number(data1[0][index]['parent']),20, 'black']);
                }
                
                  var options = {
                    colors: ['black', 'black', 'black'],
                     maxFontSize: 15,
                     minFontSize:14,
                    wordtree: {
                      format: 'explicit',
                      type: 'suffix'
                    }
                  };

                  var wordtree = new google.visualization.WordTree(document.getElementById('wordtree_explicit'));
                  wordtree.draw(data, options);
                }

            $('.h').text(data1[1]);   
            $('.l').text(data1[2]);    
            $('.table').html(data1[3]);

        },
            error: function(){
                alert('error');
            }
        });
    }

});


