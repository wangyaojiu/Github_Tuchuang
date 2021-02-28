<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>药酒对象存储丨基于jsdelivr加速的Github仓库</title>
	<Meta name="keywords" content="王药酒,王药酒图床,Github图床,图床图片外链,Jsdelivr加速图床,Yaojiu," />
	<Meta name="description" content="王药酒丨王药酒的图床丨基于Jsdelivr加速的Github外链图床丨文件上传Github并获取外链">
	    <link rel="shortcut icon" href="https://cdn.jsdelivr.net/gh/wangyaojiu/CDN@1.0/index/logo.ico" alt="logo.ico" />
    <link href="https://cdn.bootcdn.net/ajax/libs/zui/1.9.2/css/zui.min.css" rel="stylesheet">
    <style>
        .cards-wrapper {
            margin: 1%;
        }

        .progress {
            margin: 9px;
            margin-bottom: 10px;
        }

        .main-buttons-wrapper {
            margin: 1%;
        }

        .button-wrapper {
            margin: 9px;
            margin-top: 18px;
        }

        .button-wrapper > button {
            margin-right: 1%;
        }
    </style>
</head>
<body background="https://raw.sevencdn.com/haoow/Yos/main/20210228/c85de64ccbf28c6b23711dbb4c15bbfc/c85de64ccbf28c6b23711dbb4c15bbfc.png">
<script src="https://cdn.bootcdn.net/ajax/libs/vue/2.6.9/vue.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/zui/1.9.2/js/zui.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/axios/0.21.0/axios.min.js"></script>
<script src="https://cdn.bootcdn.net/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>
<script type="text/javascript">
    function displayTip(info, t) {
        new $.zui.Messager(info, {
            type: t,
            time: 2000,
            placement: 'center'
        }).show();
    }
</script><br>
<div align="center">
    <img src='https://cdn.jsdelivr.net/gh/wangyaojiu/CDN@1.2/index/index.jpg' width='250' height='250' alt='/index.jpg' />
</br>
      <font size="6">药酒图床|对象存储</font></br></br>
    



</div>
<div class="main-buttons-wrapper">
    <div class="btn-group">
        <button class="btn btn-lg btn-primary" onclick="chooseFile()">选择文件或拖拽</button>
        <input type="file" id="fileDialog" style="display:none" multiple="">
        <button class="btn btn-lg" onclick="uploadAll()">上传文件</button>
    </div>
</div>
<div class="cards-wrapper">
    <div class="cards">
        <div class="col-md-4 col-sm-6 col-lg-3" v-for="(item, index) in items">
            <div class="card">
                <div class="card-heading">
                    <strong>{{ item.filename }}</strong>
                </div>
                <div class="progress progress-striped" v-bind:class="{ active: item.hasFinished === false }">
                    <div class="progress-bar progress-bar-success" role="progressbar"
                         :aria-valuenow="item.progress" aria-valuemin="0" aria-valuemax="100"
                         v-bind:style="{width: item.progress+'%'}">
                        <span class="sr-only">{{ item.progress }}%</span>
                    </div>
                </div>
                <div class="button-wrapper">
                    <button class="btn btn-danger" type="button" v-on:click="deleteItem(index)">
                        <i class="icon icon-times"></i>
                    </button>
                    <button class="btn btn-success" type="button" v-if="item.hasFinished === true"
                            :data-clipboard-text="item.cdnurl">
                        <i class="icon icon-copy"></i>
                        JsDelivr

                    </button>
                    <button class="btn btn-success" type="button" v-if="item.hasFinished === true"
                            :data-clipboard-text="item.githuburl">
                        <i class="icon icon-copy"></i>
                        7ED-CDN
                    </button>
                    <button class="btn btn-success" type="button" v-if="item.hasFinished === true"
                            :data-clipboard-text="item.sevencdn">
                        <i class="icon icon-copy"></i>
                        Github
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    let dataWrapper = [];
    let cards_vue = new Vue({
        el: '.cards',
        data: {
            items: dataWrapper
        },
        methods: {
            deleteItem: function (index) {
                this.items.splice(index, 1);
            }
        }
    });
    let clipboard = new ClipboardJS('.btn');
    clipboard.on('success', function (e) {
        displayTip('复制成功!', 'success');
    });
    function checkFile(file) {
        const notAllowedFormat = ['doc', 'docx', 'fasta', 'xlsx', 'exe', 'xls', 'ppt', 'pptx', 'jar', 'lnk', 'apk'];
        let suffix = file.name.split('.').pop();
        if(notAllowedFormat.indexOf(suffix) > -1) {
            return suffix + '为不支持的文件格式!';
        }
        if(file.size > 15728640) {
            return file.name + '太大了，限15MB以内!';
        }
        return true;
    }
    function addFile(file) {
        let checkResult = checkFile(file);
        if(checkResult === true) {
            let reader = new FileReader();
            let b64 = {data: ''};
            reader.onload = function (e) {
                b64['data'] = e.target.result;
            }
            reader.readAsDataURL(file);
            data = {
                filename: file.name,
                base64: b64,
                hasFinished: false,
                progress: 0,
                cdnurl: '',
                githuburl: '',
                isUploading: false
            };
            dataWrapper.push(data);
        }
        else {
            displayTip(checkResult, 'danger');
        }
    }
    function chooseFile() {
        $("#fileDialog").click();
    }
    function uploadSingleFile(dataDict) {
        let dataJsonStr = JSON.stringify({'filename': dataDict['filename'],
            'content': dataDict['base64']['data'].split(',')[1]});
        $.ajax({
            url: 'api.php',
            type: 'post',
            async: true,
            dataType: 'text',
            scriptCharset: 'utf-8',
            contentType: 'application/json',
            jsonpCallback: 'jsonpCallback',
            cache: false,
            processData: false,
            data: dataJsonStr,
            xhr: function () {
                myXhr = $.ajaxSettings.xhr();
                if(myXhr.upload) {
                    myXhr.upload.addEventListener('progress', function (e) {
                        let loaded = e.loaded;
                        let total = e.total;
                        let percent = Math.floor(100*loaded/total);
                        dataDict.progress = percent;
                    }, false);
                }
                return myXhr;
            },
            success: function (result) {
                if(result.split(':')[0] === 'Curl Error') {
                    displayTip('文件' + dataDict['filename'] + ': PHP服务端传送到Github失败!', 'danger');
                    return;
                }
                let tempList = result.split(' ');
                dataDict.cdnurl = tempList[0];
                dataDict.sevencdn = tempList[1];
                dataDict.githuburl = tempList[2];
                dataDict.hasFinished = true;
            },
            error: function (error) {
                displayTip('文件' + dataDict['filename'] + ': 传送到PHP服务端失败!', 'danger');
            }
        })
    }
    function uploadAll() {
        for(let i=0; i<dataWrapper.length; i++) {
            let data = dataWrapper[i];
            uploadSingleFile(data);
        }
    }
</script>
<script type="text/javascript">
    window.addEventListener("dragenter", function(event) { event.preventDefault(); }, false);
    window.addEventListener("dragover", function(event) { event.preventDefault(); }, false);
    window.addEventListener("drop", function(event) {
        event.preventDefault();
        let files = event.dataTransfer.files;
        for (let i=0; i<files.length; i++) {
            let file = files[i];
            addFile(file);
        }
    }, false);
    $("#fileDialog").change(function (event) {
        event.preventDefault();
        let files = event.target.files;
        for (let i=0; i<files.length; i++) {
            let file = files[i];
            addFile(file);
        }
        $("#fileDialog").each(function(){ $(this).val(''); });
    });
</script>
</br></br></br></br></br></br>
<div align="center">
    
    		
    	<font size="2" >使用说明:</font></br>
    	<font size="2" >1、选择文件或拖拽</font></br>
    	<font size="2" >2、点击发布文件直至完成</font></br>
    	<font size="2" >3、点击选择复制直链URL</font></br>
    	<font size="2" >4、jsdelivr加速文件大小限制20M</font></br>
    	<font size="2" >5、国内推荐JsDelivr和7EDN|GitHub RAW国内无法直链</font></br>
  <p><a href="https://github.com/huanghaozi/file2link" target="_blank" rel="nofollow">基于JsDelivr CDN的Github图床 | 项目GitHub仓库</a></br>
  <a href="https://hell0.us/2api" target="_blank" rel="nofollow"> 随机图API</a>
   <a href="https://hell0.us" target="_blank" rel="nofollow">| 短链平台</a></p>
  
  

    
</div>
<script type="text/javascript">document.write(unescape("%3Cspan id='cnzz_stat_icon_1279711875'%3E%3C/span%3E%3Cscript src='https://s9.cnzz.com/z_stat.php%3Fid%3D1279711875%26show%3Dpic1' type='text/javascript'%3E%3C/script%3E"));</script>
</body>
</html>
