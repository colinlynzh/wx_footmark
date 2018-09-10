<template>

    <div>
    <panel :header="$t('用户列表')"  :list="list" :type="type" @on-img-error="onImgError"></panel>
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
              'todal_customers' : 0
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
        this.getList();
  },
  methods: {
    getList () {
        let _this = this;
        // let paramsData = { 'data': { 'searchForm': _this.searchForm } };
        axios.get('/api/customers').then(response => {
            let { status, data, message } = response.data;
            _this.list = data.lists;
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