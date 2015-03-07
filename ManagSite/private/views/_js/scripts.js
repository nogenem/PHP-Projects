//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;

        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });
});

$(document).ready(function() {
    var url = window.location.pathname; 
    var title = $(this).attr('title');

    // Soh pode escolher 2 opçoes de dias da semana 
    $('#week_day_select').on('change', function() {
        if (this.selectedOptions.length < 3) {
            $(this).find(':selected').addClass('selected');
            $(this).find(':not(:selected)').removeClass('selected');
        }else
            $(this)
            .find(':selected:not(.selected)')
            .prop('selected', false);
    });

    $('#datepicker').datepicker({
        format: "dd/mm/yyyy",
        clearBtn: true,
        autoclose: true,
        language: "pt-BR",
        todayHighlight: true
    });


    var aba = null
    var active_sels = ['#tablist > li:eq(0)', '.tab-content > div:eq(0)']; //por padrao, ativa a 1* aba

    if(url.match(/\/\w+\/edit\/\w+\/\d+/) || url.match(/\/\w+\/add/)){
        aba = /edit\/(\w+)\//.exec(url);
        aba = aba ? aba[1] : null;
        $('#add-midia-modal').modal();
    }else if(url.match(/\/\w+\/del\/\w+\/\d+/)){
        aba = /del\/(\w+)\//.exec(url)[1];        
        $('#delete-midia-modal').modal();
    }

    if(aba){
        active_sels[0] = '#tablist > li[name='+aba+']';
        active_sels[1] = '#'+aba;
    }
    $(active_sels[0]).addClass('active').attr("aria-expanded","true");
    $(active_sels[1]).addClass('in active').attr("aria-expanded","true");


    $('#searchBtn').click(function(event) {
        $('#searchForm').submit();
    });

    var menuid = '#'+ title.replace(' ', '_');
    if(menuid.indexOf('Movies')>=0){
        $('#movie_collapse_head').click();
        $(menuid).addClass('my_active');
    }else{
        $(menuid).addClass('active');
    }
});

String.prototype.format = function () {
  var args = arguments;
  return this.replace(/\{(\d+)\}/g, function (m, n) { return args[n]; });
};
