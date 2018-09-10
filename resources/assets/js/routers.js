import CreateArticle from './components/article/CreateArticleComponent.vue'
import Footmark from './components/FootmarkComponent.vue'
import Ucenter from './components/UCenterComponent.vue'
import UcenterEdit from './components/UCenterEditComponent.vue'
// 文章管理
import ArticleLists from './components/article/ListArticleComponent.vue';
//游客记录
import Interacts from './components/InteractsComponent.vue';
//上传图片
import UploadFigureurl from './components/UploadFigureurlComponent.vue';
//人脉关系 from './components/UploadFigureurlComponent.vue';
import InteractDetail from './components/InteractDetailComponent.vue';
//钱包明细
import VcoinDetail from './components/VcoinDetailComponent.vue';
//客户明细
import CustomerDetail from './components/CustomerDetailComponent.vue';
const routers = [
  {
    path: '/create',
    name: 'create',
    component: CreateArticle
  },
  {
    path: '/footmark',
    name: 'footmark',
    component: Footmark
  },
  {
    path: '/interacts',
    name: 'interacts',
    component: Interacts
  },
  {
    path: '/ucenter',
    name: 'ucenter',
    component: Ucenter,
    children : [
        
    ]
  },
  {
    path: '/ucenter/edit',
    name: 'ucenteredit',
    component: UcenterEdit
  },  
  {
    path: '/',
    component: CreateArticle
  },
  {
    path: '/articles',
    component: ArticleLists,
    name: '文章管理',
    children: [
      { path: 'index', component: CreateArticle, name: '文章列表' },
    
    ]
  },
  {
    path: '/user-figureurl',
    name: 'uploadfigureurl',
    component: UploadFigureurl
  },
  {
    path: '/interactdetail/:type/:id',
    name: 'interactdetail',
    component: InteractDetail
  },
  {
    path: '/vcoin/detail',
    name: 'vcoindetail',
    component: VcoinDetail
  },
  {
    path: '/customer/detail',
    name: 'customerdetail',
    component: CustomerDetail
  }
  

]


export default routers
