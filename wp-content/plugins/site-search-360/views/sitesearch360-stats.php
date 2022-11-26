<div id="stats-block" class="wrapper wrapper--narrow" style="display: none;">
    <div class="block block--first">
        <div class="flex flex--column flex--center">
            <section class="flex flex--column w-100 m-b-1" id="time-chart-block">
                <h2><?php esc_html_e('Queries over time', 'site-search-360'); ?></h2>
                <div class="chart">
                    <div id="time-chart">
                    
                    </div>
                </div>
            </section>            
            <div class="flex w-100 column--sm">
                <section class="flex flex--column flex--1 p-r-3 p-r-0--sm">
                    <h2><?php esc_html_e('Frequent queries','site-search-360') ?></h2>
                    <table class="table">
                        <thead class="table__header">
                            <tr>
                                <th><?php esc_html_e('Query','site-search-360') ?></th>
                                <th><?php esc_html_e('Count','site-search-360') ?></th>
                                <!-- <th><?php esc_html_e('CTR','site-search-360') ?></th> -->
                                <th><?php esc_html_e('Results','site-search-360') ?></th>
                            </tr>
                        </thead>
                        <tbody class="table__body" id="frequent-queries">

                        </tbody>
                    </table>
                </section>
                <section class="flex flex--column flex--1 p-l-3 p-l-0--sm">
                    <h2><?php esc_html_e('Frequent search terms','site-search-360') ?></h2>
                    <table class="table">
                        <thead class="table__header">
                            <tr>
                                <th><?php esc_html_e('Query','site-search-360') ?></th>
                                <th><?php esc_html_e('Count','site-search-360') ?></th>
                                <th><?php esc_html_e('Results','site-search-360') ?></th>
                            </tr>
                        </thead>
                        <tbody class="table__body" id="frequent-words">

                        </tbody>
                    </table>
                </section>
            </div>
        </div>
        <section class="flex flex--center">
            <a class="cp-link" target="_blank" href="<?php echo $ss360_jwt; ?>"><?php esc_html_e('See more.', 'site-search-360'); ?></a>
        </section>
    </div>
</div>

<script type="text/javascript">
    (function(){
        var queryData = undefined;
        var isChartistLoaded = false;
        jQuery.get("https://api.sitesearch360.com/sites/quickStat?token=<?php echo urlencode(get_option('ss360_api_token')); ?>").done(function(data){
            queryData = data;
            if(isChartistLoaded){
                drawCharts();
            }
        }).fail(console.warn);

        window.onChartistLoaded = function(){
            isChartistLoaded = true;
            if(queryData!==undefined){
                drawCharts();
            }
        };

        var drawCharts = function(){
            if(queryData.timeChart.queriesByTime.reduce(function(acc, entry){return acc + entry.count}, 0) === 0){
                return; //don't show empty statistics block
            }
            var statsBlock = jQuery("#stats-block"); 
            if(queryData.timeChart.queriesByTime.length < 2) {
                jQuery("#time-chart-block").hide();
            } else {
                drawQueryChart(queryData.timeChart.queriesByTime);
            }
            drawFrequentChart("#frequent-queries", queryData.frequentQueries, "query");
            drawFrequentChart("#frequent-words", queryData.frequentWords, "word");
            statsBlock.fadeIn();
        }

        var drawQueryChart = function(data){
            var labels = [];
            var series = [];
            var isMobile = ('matchMedia' in window) ? window.matchMedia('(max-width: 767px)').matches : (document.documentElement.clientWidth || window.innerWidth || document.body.clientWidth) < 768;
            data.forEach(function(entry, idx){
                labels.push((!isMobile || idx === 0 || idx === (data.length - 1)) ? new Date(entry.timestamp * 1000).toLocaleDateString() : '');
                series.push(entry.count);
            });
            new Chartist.Line("#time-chart", {
                labels: labels,
                series: [series]
            }, {low: 0, showArea: true, height: 300});
        };

        var drawFrequentChart = function(tbodySelector, data, queryKey){
            var tbody = jQuery(tbodySelector);
            data.forEach(function(entry){
                tbody.append(jQuery("<tr><td>"+entry[queryKey]+"</td><td>"+entry.count+"</td><td>"+Math.round(entry.avg)+"</td></tr>"));
            });
        }
    }());
</script>


<link href="<?php echo plugins_url('assets/chartist.min.css',  dirname(__FILE__))  ?>" rel="stylesheet">
<script src="<?php echo plugins_url('assets/chartist.min.js',  dirname(__FILE__))  ?>" onload="onChartistLoaded()"></script>

<style>
    @import url('https://fonts.googleapis.com/css?family=Open+Sans:400,600,700');
    .ct-grids {
        display: none;  
    }

    .ct-series-a .ct-area{
        fill: #3D8FFF;
        fill-opacity: 0.7;
    }

    .ct-series-a .ct-point {
        stroke: #3D8FFF;
    }

    .ct-series-a .ct-line {
        stroke: #3D8FFF;
        stroke-opacity: 0.9;
    }

    .ct-label{
        font-family: 'Open Sans', sans-serif;
        font-weight: 600;
        font-size: 12px;
        color: #4A4F62;
    }
</style>