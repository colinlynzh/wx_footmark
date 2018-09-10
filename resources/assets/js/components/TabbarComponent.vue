<template>
  <div>
   
    <tabbar>
      <tabbar-item link="/create">
        <img slot="icon" src="images/icon/icon_compose.png">
        <span slot="label">创建</span>
      </tabbar-item>
      <!-- <tabbar-item>
        <img slot="icon" src="images/icon/icon_discover.png">
        <span slot="label">发现</span>
      </tabbar-item> -->
      <tabbar-item link="/footmark">
        <img slot="icon" src="images/icon/icon_footmark.png">
        <span slot="label">谁查看我</span>
      </tabbar-item>
      <tabbar-item link="/ucenter">
        <img slot="icon" src="images/icon/icon_ucenter.png">
        <span slot="label">个人中心</span>
      </tabbar-item>
   
    </tabbar>
  </div>
</template>

<script>
import { Tabbar, TabbarItem, Group, Cell } from 'vux'

export default {
  components: {
    Tabbar,
    TabbarItem,
    Group,
    Cell
  }
}
</script>