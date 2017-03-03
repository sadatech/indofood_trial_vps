<style type="text/css"></style>
<form action="#" id="a" class="form-horizontal" method="post">
    <div class="table-toolbar">
        <div class="row">
            <div class="col-xs-12">

                <div class="mt-element-ribbon" style="background: white;">

                    <div class="ribbon ribbon-border-hor ribbon-clip ribbon-color-danger uppercase">

                        <div class="ribbon-sub ribbon-clip"></div>Data <?php echo $title; ?>

                    </div>

                </div>

            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="portlet light bordered">

                    <div class="portlet-title">

                        <div class="caption font-dark">

                            <i class="icon-settings font-dark"></i>

                            <span class="caption-subject bold uppercase"> Data <?php echo $title; ?></span>

                        </div>
                        <div class="form-group">
                          <div class="col-md-12">
                            <!-- <select id="tl" name="tl"  class=" form-control select2"  data-width="18%"></select> -->
                            <select id="ba" name="ba" class="  form-control select2" data-width="18%"></select>
                            <select id="toko" name="toko"  class=" form-control select2"  data-width="18%"></select>
                            <select id="cabang" name="cabang"  class="select2 form-control"  data-width="18%"></select>
                            <select id="kota" name="kota"  class="select2 form-control" data-width="18%"></select>
                        </div>
                        <div class="col-md-8" style="margin-top:15px;margin-left:-15px">
                          <div class="col-md-3">
                            <input type="text" class="bs-select form-control input-small date-picker" name="startDate" placeholder="Start Date" data-width="13%">

                        </div>
                        <div class="col-md-3">
                            <input type="text" class="bs-select form-control input-small date-picker" name="endDate" placeholder="End Date" data-width="13%">
                        </div>
                        <div class="col-md-2">
                          <button type="submit" class="btn sbold green">Filter</button>
                      </div>
                  </div>
              </div>
          </form>
      </div>
  </div>
</div>

<div class="row">
    <div class="col-md-12">
      <div class="portlet light bordered">

        <div class="table-toolbar">

            <div class="row">

                <div class="col-md-6">

                    

                </div>
                <div class="btn-group" style="float: right;">

                    <a href="#" class="btn sbold green" id="excelCDetail">Download Excel</a>

                </div>
            </div>
            <table style="overflow-x: scroll;" class="table table-striped table-bordered table-hover table-checkable order-column" id="sample_1" border="1">
                <thead>
                    <tr align='center'>
                        <th rowspan="2" width='100'>Cabang</th>
                        <th rowspan="2">Nama BA</th>
                        <!-- <th rowspan="2">Nama TL</th> -->
                        <th rowspan="2">Status ( Mobile / Stay )</th>
                        <th rowspan="2">Costumer Id</th>
                        <th rowspan="2">Nama Store</th>
                        <th colspan="3">Contact</th>
                        <th colspan="5">Sampling</th>
                        <th colspan="5">Strike Sampling</th>
                    </tr>
                    <tr id="sampling">
                        <td>Contact</td>
                        <td>Switching</td>
                        <td>New Recruit</td>

                        <td>BC</td>
                        <td>BTI</td>
                        <td>Rusk </td>
                        <td>Pudding</td>
                        <td>Others</td>
                        
                        <td>BC</td>
                        <td>BTI</td>
                        <td>Rusk </td>
                        <td>Pudding</td>
                        <td>Others</td>
                    </tr>
                    <tr>
                        
                    </tr>
                </thead>
                <tbody id="dataContactTotal"> 
                    <tr>
                        <td colspan="20" id="loading"></td>
                    </tr>
                </tbody>
            </table>
            <!-- <div class="btn-group">
                <a href="#" class="btn sbold green" id="excelCDetail">Download Excel</a>
            </div> -->
        </div>

    </div>
</div>
</div>

</div>
</div>