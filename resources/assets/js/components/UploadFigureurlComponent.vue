<template>

    <div class="u_upload">
    <p class="user center"><img :src="user.xq_code" class="avatar"></p>
    <vue-core-image-upload
      :class="['btn', 'btn-primary']"
      crop="true"
      resize="local"
      compress="50"
      @imageuploaded="imageuploaded"
      @errorhandle="errorhandle"
      :headers="uploadHeaders"
      :data="data"
      :max-file-size="2242880"
      url="/api/figureurl"
      text="上传微信二维码图片" >
    </vue-core-image-upload>
    </div>

</template>
<script>
import VueCoreImageUpload from 'vue-core-image-upload';

export default {
  components: {
    VueCoreImageUpload 
  },
  data () {
        return {
            data : {
              src: 'http://img1.vued.vanthink.cn/vued0a233185b6027244f9d43e653227439a.png',
            },
            uploadHeaders: {
                'X-CSRF-TOKEN': document.getElementsByTagName('meta')['csrf-token'].getAttribute('content'),
            },
            user: {
                id: 0,
                mobile : '',
                name: '',
                figureurl:'/images/default.jpg',
                xq_code: '/images/xq_code_default.jpg',
                sex:0,
                vip_level :0,
                vip_time :'',
                article_count : 0,
                coin_count : 0,
                customer_count : 0, 
                company : '',
                summary : '',
                positional : '',
                province: '', // 可以通过名字，不需要填写完整
                city: '', // 也可以通过 (number) code
                district: '', // 填写错误的信息默认不处理
                work_date: '',
                
            },
        }
  },
  mounted() {
    this.getUserInfo()    
  },
  methods: {
     getUserInfo () {
        let _this = this;
        // let paramsData = { 'data': { 'searchForm': _this.searchForm } };
        axios.get('/api/profiles?base=1').then(response => {
            let { status, data, message } = response.data;
            _this.user = data;
        })
        
    },
    imageuploaded(res) {
      if (res.status == 1) {
        this.user.xq_code = res.data.save;
      } else {
          alert('上传失败');
          return false;
      }
    },
    errorhandle(res) {
        alert(res);
        return false;
    }
  }
}
</script>
<style>
.u_upload {
    text-align: center;
    padding-bottom: 40px;
    padding-top: 40px;
    
}
.avatar {
    width: 150px;
    height: 150px;
    margin-bottom: 20px;
    border-radius: 50%;
    border: 2px solid rgba(0,0,0,.05);
}
.main .components .center {
    text-align: center;
    padding-bottom: 20px;
}
p {
    font-size: 14px;
    line-height: 1.72222;
    margin: 0 0 12px;
}

.btn {
    display: inline-block;
    line-height: 30px;
    padding: 0 15px;
    border-radius: 2px;
    background: #fff;
    border: 1px solid #e7eaec;
    min-width: 46px;
    color: #323c48;
    text-align: center;
    transition: all .15s ease;
    font-size: 13px;
    cursor: pointer;
    outline: none!important;
    box-shadow: 0 1px 2px -1px hsla(0,0%,100%,.1);
    transition: all .25s ease;
}

.btn-primary, .btn.active, .submit-btn {
    color: #fff;
    background: #2ecc71;
    border-color: #2ecc71;
}
.g-core-image-upload-btn {
    position: relative;
    overflow: hidden;
}   
    
</style>