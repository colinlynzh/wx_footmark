<template>

   <div>

    <grid>
        <grid-item>
        <div class="ucenter_grid_top">
        <div class="grid_icon">
        <img slot="icon"  style="border-radius:50%;width:100px;height:100px;" v-bind:src="user.figureurl" >
        </div>
        <div class="grid_t">
            <span id="grid_name" v-text="user.name"></span>
            <span id="grid_time" v-text="user.vip_desc"></span>
        </div>
        </div>
        </grid-item>
    </grid>
    <grid>
        <grid-item :link="{ path: '/articles/index'}">
        <div class="ucenter_grid">
        <h1 v-text="user.article_count"></h1>
        我的文章>
        </div>
        </grid-item>
        <grid-item :link="{ path: '/vcoin/detail'}">
            <div class="ucenter_grid">
            <h1 v-text="user.coin_count"></h1>
            我的钱包>
            </div>
        </grid-item>
        <grid-item :link="{ path: '/customer/detail'}"  @on-item-click="onItemClick">
            <div class="ucenter_grid">
            <h1 v-text="user.customer_count"></h1>
            我的用户>
            </div>
        </grid-item>
    </grid>
    <group >
        <cell title="个人信息" value="资料未完善" is-link :link='data.user_editurl' >
            <img slot="icon" width="30" style="display:block;margin-right:5px;" src="images/icon/user_info.png">
        </cell>
        <!-- <cell title="我的微站"  is-link ><img slot="icon" width="30" style="display:block;margin-right:5px;" src="images/icon/vip.png"></cell> -->
        <cell title="我的推荐人"  :value="user.recommend" ><img slot="icon" width="30" style="display:block;margin-right:5px;" src="images/icon/vip.png"></cell>
        <!-- <cell title="名片模板设置"  is-link ><img slot="icon" width="30" style="display:block;margin-right:5px;" src="images/icon/card.png"></cell> -->
        <cell title="开通钻石会员"  is-link :link="data.vip_power_url"><img slot="icon" width="30" style="display:block;margin-right:5px;" src="images/icon/vipuser.png"></cell>
        <cell title="推荐给好友"  is-link :link="data.vip_recommend_url"><img slot="icon" width="30" style="display:block;margin-right:5px;" src="images/icon/add_user.png"></cell>
    </group>

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
               vip_power_url : '',
               vip_recommend_url : '',
               user_editurl : '/ucenter/edit'
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
            
        }
  },
  mounted() {
        this.getUserInfo();
  },
  methods: {
    getUserInfo () {
        let _this = this;
        // let paramsData = { 'data': { 'searchForm': _this.searchForm } };
        axios.get('/api/profiles').then(response => {
            let { status, data, message } = response.data;
            _this.user = data;
            _this.data.vip_power_url = data['vip_power_url'];
             _this.data.vip_recommend_url = data['vip_recommend_url'];
            // _this.options = data.options;
            // _this.$refs.pagination.pageData.per_page = parseInt(data.lists.per_page);
            // _this.$refs.pagination.pageData.current_page = parseInt(data.lists.current_page);
            // _this.$refs.pagination.pageData.total = parseInt(data.lists.total);
        })
    },
    onItemClick () {
      console.log('on')
    },
    onImgError (item, $event) {
      console.log(item, $event)
    }
    
  }
}
</script>
<style lang="less">
    .weui-grid {
        padding: 0px;
    }
    
    .ucenter_grid_top {
        height: 180px;
        
        /* display: inline-block; */
        vertical-align: middle;
        line-height: 20px;
        background-image: url("/images/user_bg.jpg");
        padding:0px;

    }
    .grid_icon {
        /* margin:10px auto; */
        float:left;
        width:40%;
        padding-top: 40px;
        text-align: center;
    }
    .grid_t {
        width: 60%;
        float:left;
        padding-top: 70px;
        font-size: 150%;
        color: #333;
    }
    #grid_time {
        font-size: 11px;
    }
    #grid_name {
        font-weight:bold;
        font-size: 14px;
    }
    .grid_t span {
        display: block;
        line-height: 20px;
        margin-bottom: 10px;
    }
    .ucenter_grid {
        height: 80px;
        text-align: center;
        padding-top: 30px;
        /* display: table-cell; */
        line-height: 14px;
        font-size: 14px;
    }
    .ucenter_grid H1 {
        line-height: 22px;
        font-size: 22px;
        padding-bottom: 5px;
        color: #333;
    }
</style>