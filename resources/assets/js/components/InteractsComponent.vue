<template>

    <div>
    <tab bar-position="top">
      <tab-item selected @on-item-click="onReadClick">阅读</tab-item>
      <tab-item @on-item-click="onShareClick">分享</tab-item>
    </tab>
        


    <panel :header="$t('互动记录列表')"  :list="list" :type="type" @on-img-error="onImgError"></panel>
    </div>

</template>
<script>
import { Tab, TabItem,Panel } from 'vux'

export default {
  components: {
    Panel,
    Tab,
    TabItem 
  },
  data () {
        return {
            data : {
              'total_footmark' : 0 
            },
            user: {
                id: 0,
                name: '',
                figureurl:'/images/default.jpg',
                sex:0,
                vip_level :0,
                vip_time :'',
                article_count : 0,
                coin_count : 0,
                customer_count : 0,
                recommend: ''
            },
            type: '1',
            list: [],
            // footer: {
            //     title: this.$t('查看更多'),
            //     url: '/articles'
            // }
        }
  },
  mounted() {
        this.getUserInfo();
        this.getList();
  },
  methods: {
    getList () {
        let _this = this;
        // let paramsData = { 'data': { 'searchForm': _this.searchForm } };
        axios.get('/api/footmarks').then(response => {
            let { status, data, message } = response.data;
            _this.list = data.lists;
            // _this.options = data.options;
            // _this.$refs.pagination.pageData.per_page = parseInt(data.lists.per_page);
            // _this.$refs.pagination.pageData.current_page = parseInt(data.lists.current_page);
            // _this.$refs.pagination.pageData.total = parseInt(data.lists.total);
        })
    },
    getUserInfo () {
        let _this = this;
        // let paramsData = { 'data': { 'searchForm': _this.searchForm } };
        axios.get('/api/profiles').then(response => {
            let { status, data, message } = response.data;
            _this.user = data;
           if (data.vip_level==0) {
            window.location.href=data['vip_power_url'];
            return false;  
           }
        })
    },
    onEvent (event) {
      console.log('on', event)
    },
    onImgError (item, $event) {
      console.log(item, $event)
    },
    onReadClick() {
        this.getList()
    },
    onShareClick() {
        let _this = this;
        axios.get('/api/shares').then(response => {
            let { status, data, message } = response.data;
            _this.list = data.lists;
        })
    }
  }
}
</script>
<style>
   
    
</style>