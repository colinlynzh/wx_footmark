<template>

   <div>
    <panel :header="$t('我的文章')" :footer="footer" :list="list" :type="type" @on-img-error="onImgError"></panel>
    </div>

</template>
<script>
import { Grid, GridItem,Group, GroupTitle, XTable, LoadMore, Cell, CellBox, Panel } from 'vux'

export default {
  components: {
    XTable,
    LoadMore, 
    Cell,
    Panel,
    Group,
    CellBox,
    Grid,
    GridItem, 
    GroupTitle, 
  },
  data () {
        return {
            type: '5',
            list: [],
            footer: {
                title: this.$t('查看更多'),
                url: '/articles'
            }
        }
  },
  mounted() {
        this.getList();
  },
  methods: {
    getList () {
        let _this = this;
        // let paramsData = { 'data': { 'searchForm': _this.searchForm } };
        axios.get('/api/articles').then(response => {
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
    }
    
  }
}
</script>
<style>
    .footmark_count {
        height:100px;
        width:50%;
        text-align:center;
        line-height:50px;
        vertical-align:middle;   
        display:table-cell;
    }
    
</style>