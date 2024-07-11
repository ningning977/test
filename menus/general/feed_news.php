<style type="text/css">
    .switch {
        position: relative;
        display: inline-block;
        width: 54px;
        height: 27px;
    }

    .switch input { 
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        -webkit-transition: .4s;
        transition: .4s;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 4px;
        bottom: 4px;
        background-color: white;
        -webkit-transition: .4s;
        transition: .4s;
    }

    input:checked + .slider {
        background-color: #9A1118;
    }

    input:focus + .slider {
        box-shadow: 0 0 1px #9A1118;
    }

    input:checked + .slider:before {
        -webkit-transform: translateX(26px);
        -ms-transform: translateX(26px);
        transform: translateX(26px);
    }

    /* Rounded sliders */
    .slider.round {
        border-radius: 34px;
    }

    .slider.round:before {
        border-radius: 50%;
    }

    .slider.round2 {
        border-radius: 4px;
    }

    .slider.round2:before {
        border-radius: 10%;
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
</style>
<?php
    echo "<input type='hidden' id='HeadeMenuLink' value = '".$_GET['p']."'>";
?>
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
                <div class="row">
                    <div class="col-lg-auto">
                        <nav>
                            <div class="nav nav-tabs" id="team-tab" role="tablist">
                                <button class="nav-link text-primary active" id="FeedNew-tab" data-bs-toggle="tab" data-bs-target="#FeedNew" type="button" role="tab" aria-controls="FeedNew" aria-selected="false"><i class="fas fa-list"></i> รายการข่าว</button>
                                <?php if ($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004") { ?>
                                    <button class="nav-link text-primary" id="New-tab" data-bs-toggle="tab" data-bs-target="#New" type="button" role="tab" aria-controls="New" aria-selected="false"><i class="fa fa-plus fa-fw"></i> เพิ่มข่าว</button>
                                <?php } ?>
                            </div>
                        </nav>
                    </div>
                </div>

                <div class="row pt-3">
                    <div class="col-lg">
                        <div class="tab-content" id="nav-tabContent">
                            <!-- รายการข่าว -->
                            <div class="tab-pane fade show active" id="FeedNew" role="tabpanel" aria-labelledby="FeedNew-tab">
                                <div class="row">
                                    <div class="col-lg">
                                        <div class="form-group" style='width: 170px;'>
                                            <label for="SelectType">เลือกหัวข้อ</label>
                                            <select class="form-select form-select-sm" name="SelectType" id="SelectType" onchange='CallData();'>
                                                <option value="ALL" selected>ทุกประเภท</option>
                                                <option value="DEV">ประกาศทีมพัฒนา</option>
                                                <option value="ACO">ประกาศบริษัท</option>
                                                <option value="PLC">นโยบาย</option>
                                                <option value="NWS">ข่าวสาร</option>
                                                <option value="ACT">กิจกรรม</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg">
                                        <div class="table-responsive">
                                            <table class='table table-sm table-hover' style='font-size: 12px;' id='Table1'>
                                                <thead>
                                                    <tr class='text-center'>
                                                        <th width="5%" class='border-top text-center'>ลำดับที่</th>
                                                        <th width="10%" class='border-top text-center'>วันที่ประกาศ</th>
                                                        <th class='border-top text-center'>หัวข้อ</th>
                                                        <th width="13%" class='border-top text-center'>ประเภทข่าว</th>
                                                        <th width="15%" class='border-top text-center'>ผู้เขียน</th>
                                                        <th width="25%" class='border-top text-center'>ถึงฝ่าย</th>
                                                        <?php if ($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004") { ?>
                                                            <th width="5%" class='border-top text-center'>แก้ไข</th>
                                                            <th width="5%" class='border-top text-center'>ลบ</th>
                                                        <?php } ?>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- เพิ่มข่าว -->
                            <div class="tab-pane fade " id="New" role="tabpanel" aria-labelledby="New-tab">
                                <form class="form" id="FeedNewForm" enctype="multipart/form-data">
                                    <div class="row">
                                        <div class="col-lg-8">
                                            <div class="form-group">
                                                <label for="txtHeader">หัวข้อ<span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm " name='txtHeader' id="txtHeader" placeholder="กรุณากรอกหัวข้อข่าว" >
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="txtChkPin">ปักหมุด<span class="text-danger">*</span></label>&nbsp;<span class="text-muted" style='font-size: 12px;'>(การปักหมุดจะทำให้ประกาศของคุณขึ้นมาอยู่ลำดับข้างบน)</span>
                                                <div>
                                                    <label class="switch">
                                                        <input type="checkbox" name='txtChkPin' id='txtChkPin'>
                                                        <span class="slider round"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="txtNewType">เลือกประเภทข่าว<span class="text-danger">*</span></label>
                                                <select class="form-select form-select-sm" name="txtNewType" id="txtNewType" >
                                                    <option value="" selected disabled>เลือกประเภทข่าว</option>
                                                    <option value="ACO">ประกาศบริษัท</option>
                                                    <?php if ($_SESSION['DeptCode'] == "DP002") { ?>
                                                        <option value="DEV">ประกาศทีมพัฒนา</option>
                                                    <?php } ?>
                                                    <option value="PLC">นโยบาย</option>
                                                    <option value="NWS">ข่าวสาร</option>
                                                    <option value="ACT">กิจกรรม</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="txtDeptCode">เลือกฝ่ายที่ต้องการแจ้ง<span class="text-danger">*</span></label>&nbsp;<span class="text-muted" style='font-size: 12px;'>(เลือกได้มากกว่า 1 รายการ)</span>
                                                <select class="selectpicker form-control form-control-sm" name="txtDeptCode[]" id="txtDeptCode" multiple >
                                                <option value="ALL" selected>ทุกฝ่าย</option>
                                                <?php
                                                $SQL = "SELECT * FROM departments";
                                                $QRY = MySQLSelectX($SQL);
                                                while ($result = mysqli_fetch_array($QRY)){ 
                                                    echo "<option value='".$result['DeptCode']."'>".$result['DeptName']."</option>";
                                                } 
                                                ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="txtFileType">เลือกประเภทเอกสารแนบ<span class="text-danger">*</span></label>
                                                <select class="form-select form-select-sm" name="txtFileType" id="txtFileType" >
                                                    <option value="" selected disabled>เลือกประเภทเอกสารแนบ</option>
                                                    <option value="NULL">ไม่มี</option>
                                                    <option value="IMG">รูปภาพ (*.jpg, *.png, *.gif)</option>
                                                    <option value="DOC">เอกสาร (*.docx, *.xlsx, *.pdf)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <label class='fw-bolder'>ระยะเวลาประกาศ<span class="text-danger">*</span></label>&nbsp;<span class="text-muted" style='font-size: 12px;'>(ถ้าไม่ต้องการให้กำหนดเวลาการแสดงผลให้เว้นว่างทั้งสองช่อง)</span>
                                            <div class='row'>
                                                <div class="col-lg">
                                                    <input type="date" class="form-control form-control-sm" name='txtStartDate' id="txtStartDate" >
                                                </div>
                                                <div class="col-lg-1 pe-lg-0 ps-lg-0 d-flex align-items-center justify-content-center">
                                                    <span>ถึง</span>
                                                </div>
                                                <div class="col-lg">
                                                    <input type="date" class="form-control form-control-sm" name='txtEndDate' id="txtEndDate" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="txtFile">แนบไฟล์<span class="text-danger">*</span></label>&nbsp;<span class="text-muted" style='font-size: 12px;'>(ถ้าไม่มีไม่ต้องแนบไฟล์)</span>
                                                <input type="file" class="form-control form-control-sm" name="txtFile[]" id="txtFile" accept=".jpg,.png,.gif,.docx,.xlsx,.pdf" multiple  >
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
                                </form>
                                <div class="row">
                                    <div class="col-lg d-flex justify-content-end">
                                        <button class='btn btn-sm btn-primary' onclick='SaveFeedNew();'><i class="fas fa-save"></i> บันทึก</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<div class="modal fade" id="ModalNews" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class='fas fa-edit' style='font-size: 15px;'></i> แก้ไขข่าว</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="form" id="EditFeedNewForm" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-lg-8">
                            <div class="form-group">
                                <label for="editHeader">หัวข้อ<span class="text-danger">*</span></label>
                                <input type="text" class="form-control form-control-sm " name='editHeader' id="editHeader" placeholder="กรุณากรอกหัวข้อข่าว" >
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="editChkPin">ปักหมุด<span class="text-danger">*</span></label>&nbsp;<span class="text-muted" style='font-size: 12px;'>(การปักหมุดจะทำให้ประกาศของคุณขึ้นมาอยู่ลำดับข้างบน)</span>
                                <div>
                                    <label class="switch">
                                        <input type="checkbox" name='editChkPin' id='editChkPin'>
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="editNewType">เลือกประเภทข่าว<span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" name="editNewType" id="editNewType" >
                                    <option value="" selected disabled>เลือกประเภทข่าว</option>
                                    <option value="ACO">ประกาศบริษัท</option>
                                    <?php if ($_SESSION['DeptCode'] == "DP002") { ?>
                                        <option value="DEV">ประกาศทีมพัฒนา</option>
                                    <?php } ?>
                                    <option value="PLC">นโยบาย</option>
                                    <option value="NWS">ข่าวสาร</option>
                                    <option value="ACT">กิจกรรม</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="editDeptCode">เลือกฝ่ายที่ต้องการแจ้ง<span class="text-danger">*</span></label>&nbsp;<span class="text-muted" style='font-size: 12px;'>(เลือกได้มากกว่า 1 รายการ)</span>
                                <select class="selectpicker form-control form-control-sm" name="editDeptCode[]" id="editDeptCode" multiple >
                                <option value="ALL" selected>ทุกฝ่าย</option>
                                <?php
                                $SQL = "SELECT * FROM departments";
                                $QRY = MySQLSelectX($SQL);
                                while ($result = mysqli_fetch_array($QRY)){ 
                                    echo "<option value='".$result['DeptCode']."'>".$result['DeptName']."</option>";
                                } 
                                ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="editFileType">เลือกประเภทเอกสารแนบ<span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" name="editFileType" id="editFileType" >
                                    <option value="" selected disabled>เลือกประเภทเอกสารแนบ</option>
                                    <option value="NULL">ไม่มี</option>
                                    <option value="IMG">รูปภาพ (*.jpg, *.png, *.gif)</option>
                                    <option value="DOC">เอกสาร (*.docx, *.xlsx, *.pdf)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-lg-8 col-xl-8">
                            <label class='fw-bolder'>ระยะเวลาประกาศ<span class="text-danger">*</span></label>&nbsp;<span class="text-muted" style='font-size: 12px;'>(ถ้าไม่ต้องการให้กำหนดเวลาการแสดงผลให้เว้นว่างทั้งสองช่อง)</span>
                            <div class='row'>
                                <div class="col-lg">
                                    <input type="date" class="form-control form-control-sm" name='editStartDate' id="editStartDate" >
                                </div>
                                <div class="col-lg-1 pe-lg-0 ps-lg-0 d-flex align-items-center justify-content-center">
                                    <span>ถึง</span>
                                </div>
                                <div class="col-lg">
                                    <input type="date" class="form-control form-control-sm" name='editEndDate' id="editEndDate" >
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="form-group">
                                <label for="editFile">แนบไฟล์<span class="text-danger">*</span></label>&nbsp;<span class="text-muted" style='font-size: 12px;'>(ถ้าไม่มีไม่ต้องแนบไฟล์)</span>
                                <input type="file" class="form-control form-control-sm" name="editFile[]" id="editFile" accept=".jpg,.png,.gif,.docx,.xlsx,.pdf" multiple  >
                            </div>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="form-group mb-3">
                                <label for="EditDocDetail">รายละเอียด <span class="text-danger">*</span></label>
                                <input type="hidden" class="form-control" name="TmpEditDocDetail" id="TmpEditDocDetail" readonly>
                                <div class="document-editor">
                                    <div class="document-editor__toolbar" id="toolbar-container-edit"></div>
                                    <div class="document-editor__editable-container">
                                        <div class="document-editor__editable" id="EditDocDetail"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" id="btn-save-reload" data-bs-dismiss="modal">ออก</button>
                <button class='btn btn-sm btn-primary' onclick='SaveEditFeedNews();'><i class="fas fa-save"></i> บันทึก</button>
                <input type="hidden" name='IDUpdate' id='IDUpdate'>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalViewNews" tabindex="-1" role="dialog" data-bs-backdrop="static" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class='fab fa-readme' style='font-size: 20px;'></i>&nbsp;&nbsp;&nbsp;รายละเอียดข่าว</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-3 col-lg-2 col-xl-1"><span class='fw-bolder'><i class="fas fa-bullhorn fa-fw"></i> ชื่อเรื่อง</span></div>
                    <div class="col"><span id='viewHeader'></span></div>
                </div>
                <div class="row">
                    <div class="col-sm-3 col-lg-2 col-xl-1"><span class='fw-bolder'><i class="fas fa-user-edit fa-fw"></i> ผู้เขียน</span></div>
                    <div class="col"><span id='viewName'></span></div>
                </div>
                <div class="row">
                    <div class="col-sm-3 col-lg-2 col-xl-1"><span class='fw-bolder'><i class="fas fa-book-reader fa-fw"></i> ถึงฝ่าย</span></div>
                    <div class="col"><span id='viewDeptCode'></span></div>
                </div>
                <div class="row">
                    <div class="col-sm-3 col-lg-2 col-xl-1"><span class='fw-bolder'><i class="fas fa-calendar-alt fa-fw"></i> วันที่</span></div>
                    <div class="col"><span id='viewSEDate'></span></div>
                </div>
                <div class="row pt-1">
                    <div class="col-lg">
                        <div class='d-flex'><span class='fw-bolder'><i class="fas fa-newspaper fa-fw"></i> รายละเอียด</span></div>
                        <div class='p-2' id='viewDetail'></div>
                    </div>
                </div>
                <div class="row pt-1">
                    <div class="col-lg">
                        <div class='d-flex align-items-center'><span class='fw-bolder'><i class="fas fa-paperclip fa-fw"></i> เอกสารแนบ</span>&nbsp;<span class="text-muted" style='font-size: 12px;'>(คลิกที่ชื่อรูปเพื่อดาวน์โหลด, คลิกที่ไฟล์เพื่อดาวน์โหลด)</span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-7">
                        <div class='border p-2 w-100' id="viewImg"></div>
                    </div>
                    <div class="col-lg-5">
                        <div class='p-2' id='viewDoc'></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" id="btn-save-reload" data-bs-dismiss="modal">ออก</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="ModalDelete" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <h5 class="modal-title" id="DelHeader"></h5>
                <p id="DelBody" class="my-4"></p>
                <button type="button" class="btn btn-secondary btn-sm me-4" data-bs-dismiss="modal">ออก</button>
                <button type="button" class="btn btn-primary btn-sm ms-4" onclick='DeleteFeedNews();'>ตกลง</button>
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
	$(document).ready(function(){
        CallHead();
	});
</script> 
<script type="text/javascript">
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
    $(document).ready(function(){
        CallData();
        sessionStorage.setItem('txtDeptCode',JSON.stringify(""));
	});

    $("#FeedNew-tab").on("click", function() {
        CallData();
    })

    function CallData() {
        $("#Table1").dataTable().fnClearTable();
        $("#Table1").dataTable().fnDraw();
        $("#Table1").dataTable().fnDestroy();
        var DataType = $("#SelectType").val();
        $("#Table1").DataTable({
            "ajax": {
                url: "menus/general/ajax/ajaxfeed_news.php?a=CallData",
                type: "POST",
                data: { DataType : DataType },
                dataType: "json",
                dataSrc: "0"
            },
            "columns": [
                { "data": "No", class: "text-center border-start border-bottom" },
                { "data": "CreateDate", class: "text-center border-start border-bottom" },
                { "data": "newsTitle", class: "border-start border-bottom" },
                { "data": "newsType", class: "border-start border-bottom" },
                { "data": "FullName", class: "border-start border-bottom border-end" },
                { "data": "deptCount", class: "border-start border-bottom border-end" },
                <?php if($_SESSION['DeptCode'] == "DP002" || $_SESSION['DeptCode'] == "DP003" || $_SESSION['DeptCode'] == "DP004") { ?>
                { "data": "Edit", class: "text-center border-start border-bottom border-end" },
                { "data": "Delete", class: "text-center border-start border-bottom border-end" },
                <?php } ?>
            ],
            "createdRow": function (row, data, dataIndex, cells) {
                if(data.rowStyle == 1) {
                    $(row).addClass("table-warning");
                }
            },
            "responsive": true, 
            "lengthChange": false, 
            "autoWidth": false,
            "pageLength": 10,
        });
    }

    function ViewData(newsID) {
        $.ajax({
            url: "menus/general/ajax/ajaxfeed_news.php?a=ViewData",
            type: "POST",
            data: { newsID : newsID, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#viewHeader").html(inval['newsTitle']);
                    $("#viewName").html(inval['FullName']);
                    $("#viewDeptCode").html(inval['DeptCode']);
                    $("#viewSEDate").html(inval['SEDate']);
                    $("#viewDetail").html(inval['Content']);
                    $("#viewImg").html(inval['DataImg']);
                    $("#viewDoc").html(inval['DataDoc']);

                    // $("#viewDetail p").addClass("m-0");

                    $("#ModalViewNews").modal("show");
                });
            }
        })
    }

    let EditDocDetail;
    DecoupledEditor
    .create( document.querySelector('#EditDocDetail'))
    .then( editor => {
        const toolbarContaineredit = document.querySelector('#toolbar-container-edit');
        toolbarContaineredit.appendChild(editor.ui.view.toolbar.element);
        EditDocDetail = editor;
    })
    .catch(error => {
        console.error(error);
    });
    function EditData(newsID) {
        $("#IDUpdate").val(newsID);
        $.ajax({
            url: "menus/general/ajax/ajaxfeed_news.php?a=EditData",
            type: "POST",
            data: { newsID : newsID, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    $("#editHeader").val(inval['newsTitle']);
                    if(inval['pinMark'] == 1) {
                        $("#editChkPin").prop('checked', true);
                    }else{
                        $("#editChkPin").prop('checked', false);
                    }
                    $("#editNewType").val(inval['newsType']);
                    var DeptCode = inval['deptCode'].split(',');
                    $("#editDeptCode").selectpicker('val', DeptCode);
                    $("#editFileType").val(inval['attachType']);
                    $("#editStartDate").val(inval['startDate']);
                    $("#editEndDate").val(inval['endDate']);
                    $("#editFile").val("");
                    EditDocDetail.setData(inval['newsContent']);
                    $("#TmpEditDocDetail").val(EditDocDetail.getData());

                    $("#ModalNews").modal("show");
                });
            }
        })
    }
    function SaveEditFeedNews() {
        var Header    = $("#editHeader").val();
        var NewType   = $("#editNewType").val();
        var FileType  = $("#editFileType").val();
        var DeptCode  = $("#editDeptCode").val().length;
        
        var Error = 0;
        if(Header == "")     { Error++; $("#editHeader").addClass("is-invalid");   } else { $("#editHeader").removeClass("is-invalid");   }
        if(NewType == null)  { Error++; $("#editNewType").addClass("is-invalid");  } else { $("#editNewType").removeClass("is-invalid");  }
        if(FileType == null) { Error++; $("#editFileType").addClass("is-invalid"); } else { $("#editFileType").removeClass("is-invalid"); }
        if(DeptCode == 0)    { Error++; }
        if(EditDocDetail.getData() == "") { Error++; }
        
        if(Error == 0) {
            $("#TmpEditDocDetail").val(EditDocDetail.getData());
            var EditFeedNewForm = new FormData($("#EditFeedNewForm")[0]);
            EditFeedNewForm.append('ChkPin',$("#editChkPin").is(":checked"));
            EditFeedNewForm.append('IDUpdate',$("#IDUpdate").val());
            $.ajax({
                url: "menus/general/ajax/ajaxfeed_news.php?a=SaveEditFeedNews",
                type: 'POST',
                dataType: 'text',
                cache: false,
                processData: false,
                contentType: false,
                data: EditFeedNewForm,
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        CallData();
                        $("#alert_header").html("<i class='fas fa-check-circle' style='font-size: 60px;'></i>");
                        $("#alert_body").html("บันทึกข้อมูลสำเร็จ");
                        $("#alert_modal").modal("show");
                        $("#ModalNews").modal("hide");
                    });
                }
            });
        }else{
            $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
            $("#alert_body").html("กรุณากรอกข้อมูลให้ครบ");
            $("#alert_modal").modal("show");
        }
    }

    function DeleteData(newsID) {
        $("#IDUpdate").val(newsID);
        $("#DelHeader").html("<i class='fas fa-exclamation-circle' style='font-size: 70px;'></i>");
        $("#DelBody").html("คุณต้องการลบรายการข่าวนี้หรือไม่");
        $("#ModalDelete").modal("show");
    }
    function DeleteFeedNews() {
        var IDDelete = $("#IDUpdate").val();
        $.ajax({
            url: "menus/general/ajax/ajaxfeed_news.php?a=DeleteFeedNews",
            type: "POST",
            data: { IDDelete : IDDelete, },
            success: function(result) {
                var obj = jQuery.parseJSON(result);
                $.each(obj,function(key,inval) {
                    CallData();
                    $("#ModalDelete").modal("hide");
                    $("#alert_header").html("<i class='fas fa-check-circle' style='font-size: 70px;'></i>");
                    $("#alert_body").html("ลบข้อมูลสำเร็จ");
                    $("#alert_modal").modal("show");
                });
            }
        })
    }

    $("#New-tab").on("click", function() {
        $("#txtHeader").val("");
        $("#txtChkPin").prop('checked', false);
        $("#txtNewType").val("");
        $("#txtDeptCode").selectpicker('val', ['ALL']);
        $("#txtFileType").val("");
        $("#txtStartDate").val("");
        $("#txtEndDate").val("");
        $("#txtFile").val("");
        DocDetail.setData("");
        $("#TmpDocDetail").val(DocDetail.getData());
    })
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

    function SaveFeedNew() {
        var Header    = $("#txtHeader").val();
        var NewType   = $("#txtNewType").val();
        var FileType  = $("#txtFileType").val();
        var DeptCode  = $("#txtDeptCode").val().length;
        
        var Error = 0;
        if(Header == "")     { Error++; $("#txtHeader").addClass("is-invalid");   } else { $("#txtHeader").removeClass("is-invalid");   }
        if(NewType == null)  { Error++; $("#txtNewType").addClass("is-invalid");  } else { $("#txtNewType").removeClass("is-invalid");  }
        if(FileType == null) { Error++; $("#txtFileType").addClass("is-invalid"); } else { $("#txtFileType").removeClass("is-invalid"); }
        if(DeptCode == 0)    { Error++; }
        if(DocDetail.getData() == "") { Error++; }
        
        if(Error == 0) {
            $("#TmpDocDetail").val(DocDetail.getData());
            var FeedNewForm = new FormData($("#FeedNewForm")[0]);
            FeedNewForm.append('ChkPin',$("#txtChkPin").is(":checked"));
            $.ajax({
                url: "menus/general/ajax/ajaxfeed_news.php?a=SaveFeedNew",
                type: 'POST',
                dataType: 'text',
                cache: false,
                processData: false,
                contentType: false,
                data: FeedNewForm,
                success: function(result) {
                    var obj = jQuery.parseJSON(result);
                    $.each(obj,function(key,inval) {
                        $("#txtHeader").val("");
                        $("#txtChkPin").prop('checked', false);
                        $("#txtNewType").val("");
                        $("#txtDeptCode").selectpicker('val', ['ALL']);
                        $("#txtFileType").val("");
                        $("#txtStartDate").val("");
                        $("#txtEndDate").val("");
                        $("#txtFile").val("");
                        DocDetail.setData("");
                        $("#TmpDocDetail").val(DocDetail.getData());

                        $("#alert_header").html("<i class='fas fa-check-circle' style='font-size: 60px;'></i>");
                        $("#alert_body").html("บันทึกข้อมูลสำเร็จ");
                        $("#alert_modal").modal("show");
                    });
                }
            });
        }else{
            $("#alert_header").html("<i class='fas fa-exclamation-circle' style='font-size: 60px;'></i>");
            $("#alert_body").html("กรุณากรอกข้อมูลให้ครบ");
            $("#alert_modal").modal("show");
        }
    }

    $("#txtDeptCode").on("change", function(){
        if($("#txtDeptCode").val().length != 1) {
            const v = $("#txtDeptCode").val()[1];
            if($("#txtDeptCode").val()[0] == 'ALL') {
                if(JSON.parse(sessionStorage.getItem('txtDeptCode')) == "") {
                    $("#txtDeptCode").selectpicker('val', []);
                    $("#txtDeptCode").selectpicker('val', [v]);
                    sessionStorage.setItem('txtDeptCode',JSON.stringify($(this).val()[0]));
                }else{
                    sessionStorage.setItem('txtDeptCode',JSON.stringify($(this).val()[0]));
                    if(JSON.parse(sessionStorage.getItem('txtDeptCode')) == 'ALL') {
                        $("#txtDeptCode").selectpicker('val', []);
                        $("#txtDeptCode").selectpicker('val', ['ALL']);
                        sessionStorage.setItem('txtDeptCode',JSON.stringify(""));
                    }
                }
            }
        }
    })

</script> 