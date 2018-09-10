<template>

    <div>

    <panel :header="$t('钱包明细列表')"  :list="list" :type="type" @on-img-error="onImgError"></panel>
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
              'todal_vcoin' : 0,
            },
            type: '2',
            list: [],
            // footer: {
            //     title: this.$t('查看更多'),
            //     url: '/articles'
            // }
        }
  },
  mounted() {
        this.getList();
  },
  methods: {
    getList () {
        let _this = this;
        // let paramsData = { 'data': { 'searchForm': _this.searchForm } };
        axios.get('/api/vcoin').then(response => {
            let { status, data, message } = response.data;
            _this.list = data.lists;
            // _this.options = data.options;
            // _this.$refs.pagination.pageData.per_page = parseInt(data.lists.per_page);
            // _this.$refs.pagination.pageData.current_page = parseInt(data.lists.current_page);
            // _this.$refs.pagination.pageData.total = parseInt(data.lists.total);
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
    }
   
  }
}
</script>
<style>
   
    
</style>