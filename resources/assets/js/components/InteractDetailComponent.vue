<template>

    <div class="u_upload">
    <group>
      <group-title slot="title">通过人脉计算，文章通过以下人脉链接</group-title>
    </group>
    <div class="content">
    <div class = 'avatar'>
    <img :src="user.figureurl">
    <p> {{ user.name }}</p>
    </div>
    <img class='arrow' src='/images/arrow-point-to-right.png'/>
    <div class = 'avatar avatar-right'>
    <img :src="from_user.figureurl" class="">
    <p>{{ from_user.name }}</p>
    </div>
    </div>
    </div>

</template>
<script>
  import { GroupTitle, Group } from 'vux'

export default {
  components: {
    GroupTitle,
    Group
  },
  data () {
        return {
            data : {
                item_id : 0,
                type : ''
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
            from_user: {
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
    this.fetchData()
    this.getDetail()
  },
  methods: {
     getDetail () {
        let _this = this;
        // let paramsData = { 'data': { 'searchForm': _this.searchForm } };
        axios.get('/api/interactdetail?type='+_this.type+'&id='+this.item_id).then(response => {
            let { status, data, message } = response.data;
            _this.user = data.user;
            _this.from_user = data.from_user;
        })
        
    },
    fetchData () {
        let _this = this;
        this.item_id = this.$route.params.id;
        this.type = this.$route.params.type;
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
.content {
    margin-top: 30px;
    width: 320px;
    height: 320px;
    margin-left: auto;
    margin-right: auto;
    padding: 10px;
}
.content img {
    
} 
.arrow {
    float: left;
    text-align: center;
    max-width: 10%;
    margin-top: 10%;
    margin-left: 5%;
    margin-right: 5%;
}
.avatar {
    text-align: center;
    float: left;
    width: 40%;
    height: 40%;
}
.avatar p{
    font-size: 16px;
    color: #999999;
    height: 20px;
    line-height: 20px;
}
.avatar img{
    border-radius: 50%;
    border: 2px solid rgba(0,0,0,.05);
    max-width: 100%;
}
.avatar-right {
    float: right;
}
.main .components .center {
    text-align: center;
    padding-bottom: 20px;
}
 
    
</style>