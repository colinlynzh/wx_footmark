<template>

   <div>

      <!-- <x-table :full-bordered='false' :content-bordered='false'>
        <thead>
          <tr>
            <th><h1>0</h1><br/>今日阅读/分享</th>
            <th><h1>0</h1><br/>累计阅读/分享</th>
          </tr>
        </thead>
      </x-table> -->
    <group>
    <cell-box is-link link="/interacts">
    <div class="footmark_count">
    <b v-text="data.today_footmark"></b><br/>
    今日阅读/分享
    <!-- <grid>
      <grid-item :label="$t('今日阅读/分享')">
      </grid-item>
      <grid-item :label="$t(' 累计阅读/分享')">
      </grid-item>
    </grid> -->
    </div>
   
    <div class="footmark_count">
    <b v-text="data.total_footmark"></b><br/>
    累计阅读/分享
    </div>
    </cell-box>
    </group>

    <panel :header="$t('文章查看记录')" :footer="footer" :list="list" :type="type" @on-img-error="onImgError"></panel>
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
            data : {
              'today_footmark' : 0,
              'total_footmark' : 0 
            },
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
        this.getFootmark();
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
    getFootmark () {
        let _this = this;
        axios.get('/api/footmark-count').then(response => {
            let { status, data, message } = response.data;
            _this.data.today_footmark = data.today;
            _this.data.total_footmark = data.total;
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