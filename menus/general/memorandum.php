<?php
    $start_year = 2022;
    $this_year  = date("Y");
    $this_month = date("m");
?>
<style type="text/css">
    .font-weight {
        font-weight: bold;
    }
    .document-editor {
        border: 1px solid var(--ck-color-base-border);
        border-radius: var(--ck-border-radius);

        /* Set vertical boundaries for the document editor. */
        height: 512px;

        /* This element is a flex container for easier rendering. */
        display: flex;
        flex-flow: column nowrap;
    }

    .document-editor__toolbar {
        /* Make sure the toolbar container is always above the editable. */
        z-index: 1;

        /* Create the illusion of the toolbar floating over the editable. */
        box-shadow: 0 0 5px hsla( 0,0%,0%,.2 );

        /* Use the CKEditor CSS variables to keep the UI consistent. */
        border-bottom: 1px solid var(--ck-color-toolbar-border);
    }

    /* Adjust the look of the toolbar inside of the container. */
    .document-editor__toolbar .ck-toolbar {
        border: 0;
        border-radius: 0;
    }

    /* Make the editable container look like the inside of a native word processor app. */
    .document-editor__editable-container {
        padding: calc( 2 * var(--ck-spacing-large) );
        background: #161618;

        /* Make it possible to scroll the "page" of the edited content. */
        overflow-y: scroll;
    }

    .document-editor__editable-container .document-editor__editable.ck-editor__editable {
        /* Set the dimensions of the "page". */
        /* width: 15.8cm; */
        min-height: 29.7cm;

        /* Keep the "page" off the boundaries of the container. */
        padding: 0.5cm 1cm 1cm;

        border: 1px hsl( 0,0%,82.7% ) solid;
        border-radius: var(--ck-border-radius);
        background: white;

        /* The "page" should cast a slight shadow (3D illusion). */
        box-shadow: 0 0 5px hsla( 0,0%,0%,.1 );

        /* Center the "page". */
        margin: 0 auto;
    }

    /* Override the page's width in the "Examples" section which is wider. */
    .main__content-wide .document-editor__editable-container .document-editor__editable.ck-editor__editable {
        width: 18cm;
    }

    /* Set the default font for the "page" of the content. */
    .document-editor .ck-content,
    .document-editor .ck-heading-dropdown .ck-list .ck-button__label {
        font: 16px/1.6;
    }

    /* Adjust the headings dropdown to host some larger heading styles. */
    .document-editor .ck-heading-dropdown .ck-list .ck-button__label {
        line-height: calc( 1.7 * var(--ck-line-height-base) * var(--ck-font-size-base) );
        min-width: 6em;
    }

    /* Scale down all heading previews because they are way too big to be presented in the UI.
    Preserve the relative scale, though. */
    .document-editor .ck-heading-dropdown .ck-list .ck-heading_heading1 .ck-button__label,
    .document-editor .ck-heading-dropdown .ck-list .ck-heading_heading2 .ck-button__label {
        transform: scale(0.8);
        transform-origin: left;
    }

    /* Set the styles for "Heading 1". */
    .document-editor .ck-content h2,
    .document-editor .ck-heading-dropdown .ck-heading_heading1 .ck-button__label {
        font-size: 2.18em;
        font-weight: normal;
    }

    .document-editor .ck-content h2 {
        line-height: 1.37em;
        padding-top: .342em;
        margin-bottom: .142em;
    }

    /* Set the styles for "Heading 2". */
    .document-editor .ck-content h3,
    .document-editor .ck-heading-dropdown .ck-heading_heading2 .ck-button__label {
        font-size: 1.75em;
        font-weight: normal;
        color: hsl( 203, 100%, 50% );
    }

    .document-editor .ck-heading-dropdown .ck-heading_heading2.ck-on .ck-button__label {
        color: var(--ck-color-list-button-on-text);
    }

    /* Set the styles for "Heading 2". */
    .document-editor .ck-content h3 {
        line-height: 1.86em;
        padding-top: .171em;
        margin-bottom: .357em;
    }

    /* Set the styles for "Heading 3". */
    .document-editor .ck-content h4,
    .document-editor .ck-heading-dropdown .ck-heading_heading3 .ck-button__label {
        font-size: 1.31em;
        font-weight: bold;
    }

    .document-editor .ck-content h4 {
        line-height: 1.24em;
        padding-top: .286em;
        margin-bottom: .952em;
    }

    /* Make the block quoted text serif with some additional spacing. */
    .document-editor .ck-content blockquote {
        font-family: Georgia, serif;
        margin-left: calc( 2 * var(--ck-spacing-large) );
        margin-right: calc( 2 * var(--ck-spacing-large) );
    }

    @media only screen and (max-width: 960px) {
        /* Because on mobile 2cm paddings are to big. */
        .document-editor__editable-container .document-editor__editable.ck-editor__editable {
            padding: 1.5em;
        }
    }

    @media only screen and (max-width: 1200px) {
        .main__content-wide .document-editor__editable-container .document-editor__editable.ck-editor__editable {
            width: 100%;
        }
    }

    /* Style document editor a'ka Google Docs.*/
    @media only screen and (min-width: 1360px) {
        .main .main__content.main__content-wide {
            padding-right: 0;
        }
    }

    @media only screen and (min-width: 1600px) {
        .main .main__content.main__content-wide {
            width: 24cm;
        }

        .main .main__content.main__content-wide .main__content-inner {
            width: auto;
            margin: 0 50px;
        }

        /* Keep "page" look based on viewport width. */
        .main__content-wide .document-editor__editable-container .document-editor__editable.ck-editor__editable {
            width: 60%;
        }
    }
</style>
<?php
    echo "<input type='hidden' id='HeadeMenuLink' value = '".$_GET['p']."'>";
?>
<!-- <script src="https://cdn.ckeditor.com/ckeditor5/35.2.1/classic/ckeditor.js"></script> -->
<script src="https://cdn.ckeditor.com/ckeditor5/35.2.1/decoupled-document/ckeditor.js"></script>


<div class="page-heading">
    <h3><span id='header1'></span></h3>
</div>
<div class='text-secondary'>
    <?php echo PathMenu($_GET['p']); ?>
</div>
<hr class='mt-1'>
<div class="overlay text-center" style="color: #151515;">
    <div>
        <i class="fas fa-spinner fa-pulse fa-fw fa-4x"></i><br/><br/>
        กำลังโหลด...
    </div>
</div>

<section class="row">
    <div class="col-lg">
        <div class="card">
            <div class="card-header">
                <h4><span id='header2'></span></h4>
            </div>
            <div class="card-body">
                <!-- CONTENT TAB -->
                <ul class="nav nav-tabs" id="main-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#MemoList" class="btn-tabs nav-link active" id="MemoList-tab" data-bs-toggle="tab" data-tabs="0" aria-controls="MemoList" aria-selected="false">
                            <i class="fas fa-list fa-fw fa-1x"></i> รายการบันทึกภายใน
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#NewMemo" class="btn-tabs nav-link" id="NewMemo-tab" data-bs-toggle="tab" data-tabs="1" aria-controls="NewMemo" aria-selected="true">
                            <i class="fas fa-plus fa-fw fa-1x"></i> เพิ่มบันทีกภายในใหม่
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#ItemMemo" class="btn-tabs nav-link" id="ItemMemo-tab" data-bs-toggle="tab" data-tabs="1" aria-controls="ItemMemo" aria-selected="true">
                            <i class="fas fa-list fa-fw fa-1x"></i> รายการอนุมัติ
                        </a>
                    </li>
                </ul>
                <!-- END CONTENT TAB -->
                <div class="tab-content">
                    <!-- TAB 0 -->
                    <div class="tab-pane fade show active" id="MemoList" role="tabpanel" aria-labelledby="MemoList-tab">
                        <div class="row mt-4">
                            <div class="col-lg-1 col-5">
                                <div class="form-group">
                                    <label for="filt_year">เลือกปี</label>
                                    <select class="form-select form-select-sm" name="filt_year" id="filt_year">
                                    <?php
                                        for($y = $this_year; $y >= $start_year; $y--) {
                                            if($y == $this_year) {
                                                $y_slct = " selected";
                                            } else {
                                                $y_slct = "";
                                            }
                                            echo "<option value='$y'$y_slct>$y</option>";
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-7">
                                <div class="form-group">
                                    <label for="filt_month">เลือกเดือน</label>
                                    <select class="form-select form-select-sm" name="filt_month" id="filt_month">
                                    <?php
                                        for($m = 1; $m <= 12; $m++) {
                                            if($m == $this_month) {
                                                $m_slct = " selected";
                                            } else {
                                                $m_slct = "";
                                            }
                                            echo "<option value='$m'$m_slct>".FullMonth($m)."</option>";
                                        }
                                        $DeptCode = $_SESSION['DeptCode'];
                                        if(($DeptCode == "DP001" || $DeptCode == "DP002")) {
                                            $opt_dis = NULL;
                                        } else {
                                            $opt_dis = " disabled";
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-2 col-6">
                                <div class="form-group">
                                    <label for="filt_team">เลือกทีม</label>
                                    <select class="form-select form-select-sm" name="filt_team" id="filt_team">
                                        <option value="ALL"<?php echo $opt_dis; ?>>ทุกทีม</option>
                                    <?php
                                        $DeptSQL = "SELECT T0.DeptCode, T0.DeptName FROM departments T0 ORDER BY T0.DeptCode ASC";
                                        $DeptQRY = MySQLSelectX($DeptSQL);
                                        while($DeptRST = mysqli_fetch_array($DeptQRY)) {
                                            if(($DeptCode != "DP001" && $DeptCode != "DP002") && ($DeptCode != $DeptRST['DeptCode'])) {
                                                switch ($DeptCode) {
                                                    case 'DP005' :
                                                    case 'DP006' :
                                                    case 'DP007' :
                                                        if ($DeptRST['DeptCode'] == 'DP005' || $DeptRST['DeptCode'] == 'DP006' || $DeptRST['DeptCode'] == 'DP007'){
                                                            $opt_dis = NULL;
                                                            //echo "esi";
                                                        }else{
                                                            $opt_dis = " disabled ";
                                                        }
                                                        break;
                                                    default :
                                                        $opt_dis = " disabled " ;
                                                    break;
                                                }

                                            } else {
                                                $opt_dis = NULL;
                                            }




                                            echo "<option value='".$DeptRST['DeptCode']."'$opt_dis>".$DeptRST['DeptName']."</option>";
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                            <div class="offset-lg-4 col-lg-3 col-6">
                                <div class="form-group">
                                    <label for="filt_table"><i class="fas fa-search fa-fw fa-1x"></i>  ค้นหา:</label>
                                    <input type="text" class="form-control form-control-sm" name="filt_table" id="filt_table" placeholder="กรุณากรอกเพื่อค้นหา..." />
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-sm table-hover table-bordered" style="font-size: 12px;">
                                <thead>
                                    <tr class="text-center">
                                        <th width="3.5%">ลำดับ</th>
                                        <th width="7%">วันที่เอกสาร</th>
                                        <th width="10%">เลขที่เอกสาร</th>
                                        <th>หัวข้อ</th>
                                        <th width="12.5%">ฝ่าย</th>
                                        <th width="7.5%">สถานะเอกสาร</th>
                                        <th width="5%"><i class="fas fa-cog fa-fw fa-1x"></th>
                                    </tr>
                                </thead>
                                <tbody id="MemoListTable"></tbody>
                            </table>
                        </div>
                    </div>
                    <!-- TAB 1 -->
                    <div class="tab-pane fade show" id="NewMemo" role="tabpanel" aria-labelledby="NewMemo-tab">
                        <form class="form" id="MemoForm" enctype="multipart/form-data">
                            <div class="memo-step1" class="need-validation" data-step="1">
                                <div class="row mt-4">
                                    <div class="col-lg-12">
                                        <h4 class="h4">กรอกรายละเอียดบันทึกภายใน</h4>
                                        <small><span class="text-danger">*</span> หมายถึง จำเป็นต้องกรอก (Required)</small>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-lg-12">
                                        <button type="button" class="btn btn-info" id="RefreshForm" onclick="ResetForm();"><i class="fas fa-sync-alt fa-fw fa-1x"></i> รีเซ็ต</button>
                                        <button type="button" class="btn btn-secondary" id="ImportMemo"><i class="fas fa-file-import fa-fw fa-1x"></i> นำเข้า</button>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-lg-2 col-4">
                                        <div class="form-group mb-3">
                                            <label for="DocDate">วันที่เอกสาร<span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" name="DocDate" id="DocDate" value="<?php echo date("Y-m-d"); ?>" min="<?php echo date("Y-m-d"); ?>" required />
                                            <input type="hidden" name="DocEntry" id="DocEntry" value="0" readonly />
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-6">
                                        <div class="form-group mb-3">
                                            <label for="DocType">ประเภทเอกสาร<span class="text-danger">*</span></label>
                                            <select class="form-select" name="DocType" id="DocType" required>
                                                <option selected disabled>กรุณาเลือก</option>
                                                <option value="MM">[MM] บันทึกภายใน</option>
                                                <option value="MP">[MP] บันทึกภายในเพื่อจ่ายเงิน</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-2 col-2">
                                        <div class="form-group mb-3">
                                            <label for="DocSecret">ชั้นความลับ</label>
                                            <select class="form-select" name="DocSecret" id="DocSecret">
                                                <option value="N">เอกสารปกติ</option>
                                                <option value="Y">เอกสารลับ</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <label for="DocTitle">หัวข้อ<span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="DocTitle" id="DocTitle" placeholder="กรุณากรอกชื่อหัวข้อ..." required />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-4">
                                        <div class="form-group mb-3">
                                            <label for="DocMention">เรียน (To)<span class="text-danger">*</span> <small class="text-muted">(เลือกได้มากกว่า 1 ท่าน)</small></label>
                                            <select class="form-control selectpicker" name="DocMention[]" id="DocMention" data-live-search="true" multiple data-selected-text-format="count"></select>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group mb-3">
                                            <label for="DocCopyTo">สำเนาถึง (CC)</label>
                                            <input class="form-control" type="text" name="DocCopyTo" id="DocCopyTo" placeholder="กรุณากรอก..." />
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group mb-3">
                                            <label for="DocAttach">เอกสารแนบ <a href="javascript:void(0);" class="text-muted" data-bs-toggle="tooltip" title="รองรับนามสกุลไฟล์รูปภาพ (*.jpg, *.jpeg, *.png) / MS Word (*.doc, *.docx) / MS Excel (*.xls, *.xlsx) / เอกสาร (*.pdf) เท่านั้น"><i class="far fa-question-circle fa-fw fa-lg"></i></a> <small class="text-muted">(เลือกได้มากกว่า 1 ไฟล์)</small></label>
                                            <input type="file" class="form-control" name="DocAttach[]" id="DocAttach" accept=".jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx,.pdf" multiple />
                                            <a href="javascript:void(0);" class="text-muted" id="btn-browse-file"><i class="fas fa-folder-open fa-fw fa-1x"></i> รายการเอกสารแนบ</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-2">
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <label for="DocDetail">รายละเอียด <span class="text-danger">*</span></label>
                                            <input type="hidden" class="form-control" name="TmpDocDetail" id="TmpDocDetail" readonly>
                                            <div class="document-editor">
                                                <div class="document-editor__toolbar" id="toolbar-container"></div>
                                                <div class="document-editor__editable-container">
                                                    <div class="document-editor__editable" id="DocDetail"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-lg-6 col-12">
                                        <div class="form-group mb-3">
                                            <label for="DocSignOff">คำลงท้าย<span class="text-danger">*</span></label>
                                            <input type="text" name="DocSignOff" id="DocSignOff" class="form-control" />
                                            <small class="text-muted">ตัวอย่าง จึงเรียนมาเพื่ออนุมัติงบดังกล่าว / จึงเรียนมาเพื่อแจ้งให้ทราบและดำเนินการ เป็นต้น</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-6 col-12">
                                        <strong>ผู้อนุมัติ<span class="text-danger">*</span> <small class="text-muted">(กรุณาเลือกชื่อตามลำดับการอนุมัติ / เลือกสูงสุดไม่เกิน 4 คน)</small></strong>
                                        <table class="table table-bordered mt-1">
                                            <thead class="text-center">
                                                <tr>
                                                    <th width="25%">ลำดับที่</th>
                                                    <th width="75%">ผู้อนุมัติ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-center">1</td>
                                                    <td class="text-center"><select class="form-control selectpicker" name="DocApprove_1" id="DocApprove_1" data-live-search="true"></select></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">2</td>
                                                    <td class="text-center"><select class="form-control selectpicker" name="DocApprove_2" id="DocApprove_2" data-live-search="true" disabled></select></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">3</td>
                                                    <td class="text-center"><select class="form-control selectpicker" name="DocApprove_3" id="DocApprove_3" data-live-search="true" disabled></select></td>
                                                </tr>
                                                <tr>
                                                    <td class="text-center">4</td>
                                                    <td class="text-center"><select class="form-control selectpicker" name="DocApprove_4" id="DocApprove_4" data-live-search="true" disabled></select></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="row mt-2">
                                    <div class="col-lg text-right">
                                        <button type="button" class="btn btn-secondary" onclick="SaveMemo(0);"><i class="fas fa-save fa-fw fa-1x"></i> บันทึกร่าง</button>
                                        <button type="button" class="btn btn-primary" onclick="SaveMemo(1);"><i class="fas fa-save fa-fw fa-1x"></i> เพิ่มบันทึกภายใน</button>
                                    </div>
                                </div>

                            </div>
                        </form>
                    </div>
                    <!-- TAB 2 -->
                    <div class="tab-pane fade show" id="ItemMemo" role="tabpanel" aria-labelledby="ItemMemo-tab">
                        <div class="row mt-4">
                            <div class="col-auto">
                                <div class="form-group">
                                    <label for="txtYaer">เลือกปี</label>
                                    <select class="form-select form-select-sm" name="txtYaer" id="txtYaer" onchange='GetItemMemo()'>
                                    <?php
                                        for($y = $this_year; $y >= $start_year; $y--) {
                                            if($y == $this_year) {
                                                $y_slct = " selected";
                                            } else {
                                                $y_slct = "";
                                            }
                                            echo "<option value='$y'$y_slct>$y</option>";
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="table-responsive">
                            <table class='table table-sm table-hover table-bordered' id='TableItemMemo' style="font-size: 12px;">
                                <thead>
                                    <tr class="text-center">
                                        <th width="3.5%" class='border'>ลำดับ</th>
                                        <th width="7%" class='border'>วันที่เอกสาร</th>
                                        <th width="10%" class='border'>เลขที่เอกสาร</th>
                                        <th class='border'>หัวข้อ</th>
                                        <th width="12.5%" class='border'>ฝ่าย</th>
                                        <th width="7.5%" class='border'>สถานะเอกสาร</th>
                                        <th width="5%" class='border'><i class="fas fa-cog fa-fw fa-1x"></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- MODAL SAVE SUCCESS -->
<div class="modal fade" id="confirm_saved" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="confirm_header"><i class="far fa-check-circle fa-fw fa-lg text-success"></i> สำเร็จ</h5>
                <p id="confirm_body" class="my-4">บันทึกข้อมูลสำเร็จ</p>
                <button type="button" class="btn btn-primary btn-sm" id="btn-save-reload" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL FILE ATTACH -->
<div class="modal fade" id="ModalAttachFile" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-folder-open fa-fw fa-1x"></i> รายการเอกสารแนบ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <table class="table table-bordered" style="font-size: 12px;">
                            <thead class="text-center">
                                <tr>
                                    <th width="7.5%">ลำดับ</th>
                                    <th>ชื่อไฟล์</th>
                                    <th width="7.5%"><i class="fas fa-trash fa-fw fa-lg"></i></th>
                                </tr>
                            </thead>
                            <tbody id="FileAttachList"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL CANCEL ALERT -->
<div class="modal fade" id="confirm_cancel" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="confirm_header"><i class="far fa-question-circle fa-fw fa-lg"></i> ยืนยันการยกเลิก</h5>
                <p id="confirm_body" class="my-4">คุณต้องการยกเลิกใบบันทึกภายในหรือไม่?</p>

                <button type="button" class="btn btn-secondary btn-sm" id="btn-cancel-dismiss" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary btn-sm" id="btn-cancel-confirm" data-docentry="0" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<!-- MODAL MEMO PREVIEW -->
<div class="modal fade" id="ModalPreviewMM" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-full modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-file fa-fw fa-lg"></i> รายละเอียดบันทึกภายใน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mt-4">
                    <div class="col-12">
                        <h5 class="h6">บันทึกภายในเลขที่: <span id="preview_DocNum"></span></h5>
                    </div>
                </div>
                <div class="row">
                    <table class="table table-borderless table-sm" style="font-size: 12px;">
                        <tr>
                            <td class="font-weight" width="15%">วันที่เอกสาร</td>
                            <td id="preview_DocDate"></td>
                        </tr>
                        <tr>
                            <td class="font-weight">เรื่อง</td>
                            <td id="preview_DocTitle"></td>
                        </tr>
                        <tr>
                            <td class="font-weight">เรียน (To)</td>
                            <td id="preview_DocMention"></td>
                        </tr>
                        <tr>
                            <td class="font-weight">สำเนาถึง (CC)</td>
                            <td id="preview_DocCopy"></td>
                        </tr>
                        <tr>
                            <td class="font-weight">ผู้จัดทำ</td>
                            <td id="preview_Create"></td>
                        </tr>
                    </table>
                </div>
                <ul class="nav nav-tabs" id="mm-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a href="#MMDetail" class="btn btn-tabs nav-link active" id="MMDetailTab" data-bs-toggle="tab" data-bs-target="#MMDetail" role="tab" data-tabs="0" aria-controls="MMDetailTab" aria-selected="false" style="font-size: 12px;">
                            <i class="fas fa-file-alt fa-fw fa-1x"></i> รายละเอียด
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#MMAttach" class="btn btn-tabs nav-link" id="MMAttachTab" data-bs-toggle="tab" data-bs-target="#MMAttach" role="tab" data-tabs="1" aria-controls="MMAttachTab" style="font-size: 12px;">
                            <i class="fas fa-paperclip fa-fw fa-1x"></i> เอกสารแนบ
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a href="#MMApprove" class="btn btn-tabs nav-link" id="MMApproveTab" data-bs-toggle="tab" data-bs-target="#MMApprove" role="tab" data-tabs="1" aria-controls="MMApproveTab" style="font-size: 12px;">
                            <i class="fas fa-tasks fa-fw fa-1x"></i> สถานะการอนุมัติ
                        </a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane show active" id="MMDetail" role="tabpanel" aria-labelledy="MMDetailTab">
                        <div class="row mt-4">
                            <div class="col-12" id="preview_DocDetail" style="font-size: 13px;"></div>
                        </div>
                    </div>
                    <div class="tab-pane" id="MMAttach" role="tabpanel" aria-labelledby="MMAttachTab">
                        <div class="row mt-4">
                            <div class="col-12">
                                <table class="table table-bordered" style="font-size: 12px;">
                                    <thead class="text-center">
                                        <tr>
                                            <th width="5%">ลำดับ</th>
                                            <th>ชื่อเอกสารแนบ</th>
                                            <th width="15%">วันที่อัพโหลด</th>
                                            <th width="7.5%"><i class="fas fa-file-download fa-fw fa-lg"></i></th>
                                        </tr>
                                    </thead>
                                    <tbody id="preview_MMAttach"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="MMApprove" role="tabpanel" aria-labelledby="MMApproveTab">
                        <div class="row mt-4">
                            <div class="col-12" id="preview_Approve">
                                <table class="table table-bordered" style="font-size: 12px;">
                                    <thead class="text-center">
                                        <tr>
                                            <th width="5%">ลำดับ</th>
                                            <th width="15%">ผู้อนุมัติ</th>
                                            <th width="10%">ผลการ<br/>พิจารณา</th>
                                            <th>หมายเหตุ</th>
                                            <th width="15%" >วันที่อนุมัติ</th>
                                        </tr>
                                    </thead>
                                    <tbody id="preview_approvelist"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../../js/extensions/DatatableToExcel/jquery.dataTables.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/dataTables.buttons.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/jszip.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.html5.min.js"></script>
<script src="../../js/extensions/DatatableToExcel/buttons.print.min.js"></script>

<script type="text/javascript">
/* CALL CKEDITOR
let DocDetail;
ClassicEditor
    .create(document.querySelector('#DocDetail'))
    .then( editor => {
        DocDetail = editor;
    })
    .catch( error => {
        console.error( error );
    });
*/
let DocDetail;
DecoupledEditor
    .create( document.querySelector('#DocDetail'))
    .then( editor => {
        const toolbarContainer = document.querySelector('#toolbar-container');
        toolbarContainer.appendChild(editor.ui.view.toolbar.element);
        DocDetail = editor;
    })
    .catch(error => {
        console.error(error);
    });

function CallHead(){
    $(".overlay").show();
    var MenuCase = $('#HeadeMenuLink').val()
    $.ajax({
        url: "menus/human/ajax/ajaxemplist.php?a=head",//แก้ บรรทัดนี้ทุกครั้ง  URL ajax เอง
        type: "POST",
        data : {MenuCase : MenuCase,},
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#header1").html(inval["header1"]);
                $("#header2").html(inval["header2"]);
            });
            $(".overlay").hide();
        }
    });
};
/* เพิ่มสคลิป อื่นๆ ต่อจากตรงนี้ */

function number_format(number,decimal) {
     var options = { roundingPriority: "lessPrecision", minimumFractionDigits: decimal, maximumFractionDigits: decimal };
     var formatter = new Intl.NumberFormat("en",options);
     return formatter.format(number)
}

function GetMemoList(filt_year,filt_month,filt_team) {
    // console.log("ทำงาน");
    $.ajax({
        url: "menus/general/ajax/ajaxmemorandum.php?p=MemoList",
        type: "POST",
        data: { y: filt_year, m: filt_month, t: filt_team },
        success: function(result) {
            var obj =jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $("#MemoListTable").html(inval['MemoList']);
            });
        }
    });
}

function ResetForm() {
    $("#DocMention, #DocApprove_1, #DocApprove_2, #DocApprove_3, #DocApprove_4").selectpicker("destroy");
    $("#MemoForm")[0].reset();
    $("#DocEntry").val(0);
    DocDetail.setData("");
    $("#DocApprove_2, #DocApprove_3, #DocApprove_4").attr("disabled",true);
    $("#DocType").attr("disabled",false);
    $("#DocMention, #DocApprove_1, #DocApprove_2, #DocApprove_3, #DocApprove_4").selectpicker();
}

function GetEmpName() {
    $("#DocMention, #DocApprove_1, #DocApprove_2, #DocApprove_3, #DocApprove_4").selectpicker("destroy");
    $.ajax({
        url: "menus/general/ajax/ajaxmemorandum.php?p=GetEmpName",
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#DocMention, #DocApprove_1, #DocApprove_2, #DocApprove_3, #DocApprove_4").append(inval['view_user']);
            });
            $("#DocMention, #DocApprove_1, #DocApprove_2, #DocApprove_3, #DocApprove_4").selectpicker();
        }
    })
}

function SaveMemo(SaveType) {

    var ErrorPoint = 0;
    var ErrorID    = [];
    var SuccessID  = [];
    var CheckID    = ["DocDate","DocType","DocTitle","DocMention","DocSignOff"];
    if(CheckID.length > 0) {
        for(let i = 0; i < CheckID.length; i++) {
            if($("#"+CheckID[i]).val() == null || $("#"+CheckID[i]).val() == "" || $("#"+CheckID[i]).val() == "NULL") {
                ErrorPoint = ErrorPoint+1;
                ErrorID.push(CheckID[i]);
            } else {
                SuccessID.push(CheckID[i]);
            }
        }
    }

    if(ErrorPoint > 0) {
        for(let i = 0; i < ErrorID.length; i++) { $("#"+ErrorID[i]).removeClass("is-valid is-invalid").addClass("is-invalid"); }
        for(let i = 0; i < SuccessID.length; i++) { $("#"+SuccessID[i]).removeClass("is-invalid is-invalid").addClass("is-valid"); }
        $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
        $("#alert_body").html("กรุณากรอกข้อมูลให้ครบถ้วน");
        $("#alert_modal").modal('show');
    } else {
        $("#TmpDocDetail").val(DocDetail.getData());
        $("#DocApprove_1, #DocApprove_2, #DocApprove_3, #DocApprove_4").selectpicker("destroy");
        $("#DocType, #DocApprove_1, #DocApprove_2, #DocApprove_3, #DocApprove_4").attr("disabled",false);
        var MemoForm = new FormData($("#MemoForm")[0]);
            MemoForm.append('SaveType',SaveType);
        /* SaveType 0 = Draft / 1 = Added */
        $.ajax({
            url: "menus/general/ajax/ajaxmemorandum.php?p=SaveMemo",
            type: 'POST',
            dataType: 'text',
            cache: false,
            processData: false,
            contentType: false,
            data: MemoForm,
            success: function() {
                $("#confirm_saved").modal('show');
                $("#btn-save-reload").on("click", function(e){
                    e.preventDefault();
                    window.location.reload();
                });
            }
        });
    }
}

function EditMM(DocEntry) {
    var DocEntry = DocEntry;
    $(".modal").modal("hide");
    $("#DocEntry").val(DocEntry);
    $("#DocMention, #DocApprove_1, #DocApprove_2, #DocApprove_3, #DocApprove_4").selectpicker("destroy");

    $.ajax({
        url: "menus/general/ajax/ajaxmemorandum.php?p=EditMM",
        type: "POST",
        data: { DocEntry: DocEntry },
        success: function(result) {
            $(".nav-tabs a[href='#NewMemo']").tab("show");
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval){
                $("#DocEntry").val(inval['DocEntry']);
                $("#DocDate").val(inval['DocDate']);
                $("#DocType").val(inval['DocType']).change().attr("disabled",true);
                $("#DocSecret").val(inval['DocSecret']);
                $("#DocTitle").val(inval['DocTitle']);
                $("#DocCopyTo").val(inval['DocCopyTo']);
                $("#DocSignOff").val(inval['DocSignOff']);
                var Mention = inval['DocMention'].split(",");
                $("#DocMention").val(Mention).change();

                var Approve = inval['UkeyReq'].split(",");
                for(var l = 0; l < Approve.length; l++) {
                    var AID = l+1;
                    if(Approve[l] != "NULL") {
                        $("#DocApprove_"+AID).val(Approve[l]).attr("disabled",false);
                    }
                    
                }
                $("#TmpDocDetail").val(inval['DocDetail']);
                $("#FileAttachList").html(inval['AttachList']);
                DocDetail.setData(inval['DocDetail']);
                $("#DocApprove_"+AID).change();
                $("#DocMention, #DocApprove_1, #DocApprove_2, #DocApprove_3, #DocApprove_4").selectpicker();
            })
        }
    });
}

function PreviewMM(docentry,int_status) {
    $("#ModalPreviewMM").modal("show");
    $(".nav-tabs a[href='#MMDetail']").tab("show");
    $.ajax({
        url: "menus/general/ajax/ajaxmemorandum.php?p=PreviewMM",
        type: "POST",
        data: { DocEntry: docentry, int_status: int_status },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval) {
                $("#preview_DocNum").html(inval['view_DocNum']);
                $("#preview_DocDate").html(inval['view_DocDate']);
                $("#preview_DocTitle").html(inval['view_DocTitle']);
                $("#preview_DocMention").html(inval['view_MentionName']);
                $("#preview_DocCopy").html(inval['view_DocCopyTo']);
                $("#preview_DocDetail").html(inval['view_DocDetail']);
                $("#preview_MMAttach").html(inval['view_attachlist']);
                $("#preview_approvelist").html(inval['view_approvelist']);
                $("#preview_Create").html(inval['view_CreateName']);
            });
        }
    });
}

function GetAttach(DocEntry) {
    $.ajax({
        url: "menus/general/ajax/ajaxmemorandum.php?p=GetAttach",
        type: "POST",
        data: { DocEntry: DocEntry },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj,function(key,inval) {
                $("#FileAttachList").html(inval['AttachList'])
            });
        }
    });
}

function DeleteAttach(DocEntry, AttachID) {
    console.log(DocEntry, AttachID);
    $.ajax({
        url: "menus/general/ajax/ajaxmemorandum.php?p=DelAttach",
        type: "POST",
        data: { AttachID: AttachID },
        success: function(result) {
            $("#ModalAttachFile").modal("hide");
            $("#confirm_saved").modal("show");
            GetAttach(DocEntry);
        }
    });
}

function PrintMM(DocEntry,int_status) {
    var DocEntry   = DocEntry;
    var int_status = int_status;
    window.open('menus/general/print/printmm.php?DocEntry='+DocEntry,'_blank');
}

function CancelMM(DocEntry) {
    var DocEntry = DocEntry;
    $(".modal").modal("hide");
    $("#confirm_cancel").modal("show");

    $("#btn-cancel-confirm").on("click", function(e) {
        e.preventDefault();
        $.ajax({
            url: 'menus/general/ajax/ajaxmemorandum.php?p=CancelMM',
            type: 'POST',
            data: { DocEntry: DocEntry },
            success: function(result) {
                $("#confirm_saved").modal("show");
                $("#btn-save-reload").on("click", function(e){
                    e.preventDefault();
                    window.location.reload();
                });
            }
        });
    });
}

function ExportMM(DocEntry) {
    var DocEntry = DocEntry;
    $.ajax({
        url: "menus/general/ajax/ajaxmemorandum.php?p=ExportMM",
        type: "POST",
        data: { DocEntry: DocEntry },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval){
                if(inval['AddStatus'] == "SUCCESS") {
                    $("#confirm_saved").modal('show');
                    $("#btn-save-reload").on("click", function(e){
                        e.preventDefault();
                        window.location.reload();
                    });
                } else {
                    var alert_header = "<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!";
                    var alert_body;
                    switch(inval['AddStatus']) {
                        case "ERR::DUPLICATE":
                            alert_body   = "ไม่สามารถเพิ่มเอกสารนี้ได้เนื่องจากเอกสารนี้ยังไม่ถูกบัญชีตีกลับในระบบรับ/ส่งเอกสารบัญชี";
                        break;
                        case "ERR::CANNOT_INSERT":
                            alert_body   = "ไม่สามารถเพิ่มเอกสารเข้าไปในฐานข้อมูลได้ กรุณาติดต่อฝ่าย IT";
                        break;
                    }
                    $("#alert_header").html(alert_header);
                    $("#alert_body").html(alert_body);
                    $("#alert_modal").modal('show');
                }
            });
        }
    });
}

$(document).ready(function(){
    CallHead();
    // StartEditor();
    GetEmpName();
    var filt_year = $("#filt_year").val();
    var filt_month = $("#filt_month").val();
    var filt_team = $("#filt_team").val();
    GetMemoList(filt_year,filt_month,filt_team);

    var tooltipTriggerList = [].slice.call($('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    GetItemMemo();
});


$("#filt_year, #filt_month, #filt_team").on("change", function(){
    var filt_year = $("#filt_year").val();
    var filt_month = $("#filt_month").val();
    var filt_team = $("#filt_team").val();
    GetMemoList(filt_year,filt_month,filt_team);
});

$("#filt_table").on("keyup", function(){
    var value = $(this).val().toLowerCase();
    $("#MemoListTable tr").filter(function() {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    });
});

$("#btn-browse-file").on("click",function(e) {
    $("#ModalAttachFile").modal("show");
    var DocEntry = parseFloat($("#DocEntry").val());
    var FileLine = "";
    if(DocEntry == 0) {
        var FileList = document.getElementById('DocAttach').files;
        if(FileList.length == 0) {
            FileLine += "<tr><td class='text-center' colspan='3'>ไม่มีเอกสารแนบ :()</td></tr>";
        } else{
            var no = 1;
            
            for(i=0;i<=FileList.length-1;i++) {
                FileLine += 
                "<tr>"+
                    "<td class='text-right'>"+number_format(no,0)+"</td>"+
                    "<td>"+FileList[i].name+"</td>"+
                    "<td class='text-center'>&nbsp;</td>"+
                "</tr>";
                console.log(FileList[i].name);
                no++;
            }
            
        }
        $("#FileAttachList").html(FileLine);
    }
});


$("select[id*='DocApprove_']").on("change", function(){
    var slctid = parseInt($(this).attr("id").slice(-1));
    var ukey   = $(this).val();
    var nextid = slctid+1;
    // console.log(nextid);
    if(ukey != "NULL") {
        var Approve_1 = $("#DocApprove_1").val();
        var Approve_2 = $("#DocApprove_2").val();
        var Approve_3 = $("#DocApprove_3").val();
        var Approve_4 = $("#DocApprove_4").val();
        let CheckUkey = [Approve_1,Approve_2,Approve_3,Approve_4];
        let Duplicate;

        if(slctid > 1) {
            for (var l = (CheckUkey.length)-1; l >= 0 ; l--) {
                if(l != slctid-1) {
                    // console.log(l,slctid,CheckUkey[l],ukey);
                    if(CheckUkey[l] == ukey && ukey != "NULL") {
                        Duplicate = true;
                        break;
                    } else {
                        Duplicate = false;
                    }
                }
            }  
        } else {
            Duplicate = false;
            $("#DocApprove_2").selectpicker("destroy").attr("disabled",false).val("NULL").selectpicker();
            $("#DocApprove_3, #DocApprove_4").selectpicker("destroy").attr("disabled",true).val("NULL").selectpicker();
        }
        // console.log(Duplicate);
        if(Duplicate == true) {
            $("#alert_header").html("<i class=\"far fa-times-circle fa-fw fa-lg text-danger\"></i> พบข้อผิดพลาด!");
            $("#alert_body").html("ไม่สามารถเลือกชื่อผู้อนุมัติเดิมได้");
            $("#alert_modal").modal('show');
            $("#DocApprove_"+slctid).selectpicker("destroy").val("NULL").change().selectpicker();
        } else {
            if($("#DocApprove_"+nextid).val() == "NULL") {
                $("#DocApprove_"+nextid).selectpicker("destroy").attr("disabled",false).val("NULL").selectpicker();
            }
        }    
    } else {
        var DisSelect = true;
        for(loop = nextid; loop <= 4; loop++) {
            $("#DocApprove_"+loop).selectpicker("destroy").attr("disabled",true).val("NULL").selectpicker();
        }
    }
});

function GetItemMemo() {
    const Year = $("#txtYaer").val();
    $.ajax({
        url: "menus/general/ajax/ajaxmemorandum.php?p=GetItemMemo",
        type: "POST",
        data: { Year: Year },
        success: function(result) {
            var obj = jQuery.parseJSON(result);
            $.each(obj, function(key,inval){
                $("#TableItemMemo tbody").html(inval['MemoList']);
                $('#TableItemMemo, #TableItemMemo').DataTable({
                    "columnDefs": [
                        { "width": "3.5%", "targets": 0 },
                        { "width": "7%", "targets": 1 },
                        { "width": "10%", "targets": 2 },
                        { "width": "12.5%", "targets": 4 },
                        { "width": "7.5%", "targets": 5 },
                        { "width": "5%", "targets": 5 }
                    ],
                    "responsive": true, 
                    "lengthChange": false, 
                    "autoWidth": false,
                    "pageLength": 15,
                    "ordering": false,
                    "language": 15
                });
            });
        }
    })
}
</script> 