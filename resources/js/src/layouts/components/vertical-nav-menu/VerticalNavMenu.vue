<!-- =========================================================================================
  File Name: VerticalNavMenu.vue
  Description: Vertical NavMenu Component
  Component Name: VerticalNavMenu
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
    Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->

<template>
  <div class="parentx">

    <vs-sidebar
      v-hammer:swipe="onMenuSwipe"
      ref="verticalNavMenu"
      v-model="isVerticalNavMenuActive"
      :click-not-close="clickNotClose"
      :reduce-not-rebound="reduceNotRebound"
      :parent="parent"
      :hidden-background="clickNotClose"
      :reduce="reduce"
      class="v-nav-menu items-no-padding"
      default-index="-1">

      <div @mouseenter="mouseEnter" @mouseleave="mouseLeave">

        <!-- Header -->
        <div slot="header" class="header-sidebar flex items-end justify-between">

          <!-- Logo -->
          <router-link tag="div" class="vx-logo cursor-pointer flex items-center" to="/">
            <logo class="fill-current text-primary" />
            <!-- <span v-show="isMouseEnter || !reduce" v-if="title" class="vx-logo-text text-primary v-nav-text-border">{{ title }}</span> -->
          </router-link>
          <!-- /Logo -->

          <!-- Menu Buttons -->
          <div>
            <!-- Close Button -->
            <template v-if="showCloseButton">
              <feather-icon icon="XIcon" class="m-0 cursor-pointer" @click="$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', false)" />
            </template>

            <!-- Toggle Buttons -->
            <template v-else-if="!showCloseButton && !verticalNavMenuItemsMin">
              <feather-icon
                id="btnVNavMenuMinToggler"
                :icon="reduce ? 'CircleIcon' : 'DiscIcon'"
                class="mr-0 cursor-pointer"
                svg-classes="stroke-current text-primary"
                @click="toggleReduce(!reduce)" />
            </template>
          </div>
          <!-- /Menu Toggle Buttons -->
        </div>
        <!-- /Header -->

        <!-- Header Shadow -->
        <div v-show="showShadowBottom" class="shadow-bottom" />

        <!-- Menu Items -->
        <component ref="verticalNavMenuPs" :is="scrollbarTag" :key="$vs.rtl" :settings="settings" class="scroll-area-v-nav-menu pt-2" @ps-scroll-y="psSectionScroll" @scroll="psSectionScroll">
          <template v-for="(item, index) in menuItemsUpdated">
            <!-- Group Header -->
            <span v-if="item.header && !verticalNavMenuItemsMin" :key="`header-${index}`" class="navigation-header truncate">
              {{ $t(item.i18n) || item.header }}
            </span>
            <!-- /Group Header -->

            <template v-else-if="!item.header && !item.hidden">
              <template v-if="item.children">
                <template v-if="checkUnhiddenLink(item.children)">
                  <v-nav-menu-group
                    :key="`group-${index}`"
                    :open-hover="openGroupHover"
                    :group="item"
                    :group-index="index"
                    :open="isGroupActive(item)" />
                </template>
                <template v-else>
                  <v-nav-menu-item
                    :key="`item-${index}`"
                    :index="index"
                    :to="item.slug !== 'external' ? item.path : null"
                    :href="item.slug === 'external' ? item.path : null"
                    :icon="item.icon"
                    :target="item.target"
                    :is-disabled="item.isDisabled"
                    :slug="item.slug">
                    <span v-show="!verticalNavMenuItemsMin" class="truncate">{{ $t(item.i18n) || item.name }}</span>
                    <vs-chip v-if="item.tag && (isMouseEnter || !reduce)" :color="item.tagColor" class="ml-auto">{{ item.tag }}</vs-chip>
                  </v-nav-menu-item>
                </template>
              </template>
              <template v-else>
                <template v-for="(item_children, index_children) in item.children">
                  <!-- Nav-Item -->
                  <!-- {{ index_children }} -->
                  <v-nav-menu-item
                    v-if="!item_children.children"
                    :key="`item-${index_children}`"
                    :index="index_children"
                    :to="item_children.slug !== 'external' ? item_children.path : null"
                    :href="item_children.slug === 'external' ? item_children.path : null"
                    :icon="item_children.icon"
                    :target="item_children.target"
                    :is-disabled="item_children.isDisabled"
                    :slug="item_children.slug">
                    <span v-show="!verticalNavMenuItemsMin" class="truncate">{{ $t(item_children.i18n) || item_children.name }}</span>
                    <vs-chip v-if="item_children.tag && (isMouseEnter || !reduce)" :color="item_children.tagColor" class="ml-auto">{{ item_children.tag }}</vs-chip>
                  </v-nav-menu-item>

                  <!-- Nav-Group -->
                  <template v-else>
                    <v-nav-menu-group
                      :key="`group-${index_children}`"
                      :open-hover="openGroupHover"
                      :group="item_children"
                      :group-index="index_children"
                      :open="isGroupActive(item_children)" />
                  </template>
                <!-- /Nav-Group -->
                </template>
              </template>

            </template>
          </template>
        </component>
        <!-- /Menu Items -->
      </div>
    </vs-sidebar>

    <!-- Swipe Gesture -->
    <div
      v-hammer:swipe="onSwipeAreaSwipe"
      v-if="!isVerticalNavMenuActive"
      class="v-nav-menu-swipe-area" />
      <!-- /Swipe Gesture -->
  </div>
</template>

<script>
import VuePerfectScrollbar from 'vue-perfect-scrollbar';
import VNavMenuGroup from './VerticalNavMenuGroup.vue';
import VNavMenuItem from './VerticalNavMenuItem.vue';

import Logo from '../Logo.vue';

export default {
  name: 'VNavMenu',
  components: {
    VNavMenuGroup,
    VNavMenuItem,
    VuePerfectScrollbar,
    Logo,
  },
  props: {
    logo: { type: String, default: '' },
    openGroupHover: { type: Boolean, default: false },
    parent: { type: String, default: '' },
    reduceNotRebound: { type: Boolean, default: true },
    navMenuItems: { type: Array, required: true },
    title: { type: String, default: '' },
  },
  data: () => ({
    clickNotClose: false, // disable close navMenu on outside click
    isMouseEnter: false,
    reduce: false, // determines if navMenu is reduce - component property
    showCloseButton: false, // show close button in smaller devices
    settings: { // perfectScrollbar settings
      maxScrollbarLength: 60,
      wheelSpeed: 1,
      swipeEasing: true,
    },
    showShadowBottom: false,
  }),
  computed: {
    isGroupActive() {
      return (item) => {
        const path = this.$route.fullPath;
        const routeParent = this.$route.meta ? this.$route.meta.parent : undefined;
        let open = false;

        const func = (item) => {
          if (item.children) {
            item.children.forEach((item) => {
              if (item.path && (path === item.path || routeParent === item.slug)) {
                open = true;
              } else if (item.children) {
                func(item);
              }
            });
          }
        };
        func(item);
        return open;
      };
    },
    menuItemsUpdated() {
      const clone = this.navMenuItems.slice();
      for (const [index, item] of this.navMenuItems.entries()) {
        if (item.header && item.items.length && (index || 1)) {
          const i = clone.findIndex(ix => ix.header === item.header);
          for (const [subIndex, subItem] of item.items.entries()) {
            clone.splice(i + 1 + subIndex, 0, subItem);
          }
        }
      }

      return clone;
    },
    isVerticalNavMenuActive: {
      get() {
        return this.$store.state.isVerticalNavMenuActive;
      },
      set(val) {
        this.$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', val);
      },
    },
    layoutType() {
      return this.$store.state.mainLayoutType;
    },
    reduceButton: {
      get() {
        return this.$store.state.reduceButton;
      },
      set(val) {
        this.$store.commit('TOGGLE_REDUCE_BUTTON', val);
      },
    },
    isVerticalNavMenuReduced() {
      return Boolean(this.reduce && this.reduceButton);
    },
    verticalNavMenuItemsMin() {
      return this.$store.state.verticalNavMenuItemsMin;
    },
    scrollbarTag() {
      return this.$store.getters.scrollbarTag;
    },
    windowWidth() {
      return this.$store.state.windowWidth;
    },
  },
  watch: {
    '$route'() {
      if (this.isVerticalNavMenuActive && this.showCloseButton) {
        this.$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', false);
      }
    },
    reduce(val) {
      const verticalNavMenuWidth = val ? 'reduced' : this.$store.state.windowWidth < 1200 ? 'no-nav-menu' : 'default';
      this.$store.dispatch('updateVerticalNavMenuWidth', verticalNavMenuWidth);

      setTimeout(function() {
        window.dispatchEvent(new Event('resize'));
      }, 100);
    },
    layoutType() {
      this.setVerticalNavMenuWidth();
    },
    reduceButton() {
      this.setVerticalNavMenuWidth();
    },
    windowWidth() {
      this.setVerticalNavMenuWidth();
    },
  },
  mounted() {
    this.setVerticalNavMenuWidth();
  },
  methods: {
    checkUnhiddenLink(children){
      var output = false;
      children.filter(child => {
        if (child.hidden === false) {
          output = true;
        }
      });
      return output;
    },
    onMenuSwipe(event) {
      if (event.direction === 4 && this.$vs.rtl) {
        // Swipe Right
        if (this.isVerticalNavMenuActive && this.showCloseButton) {
          this.isVerticalNavMenuActive = false;
        }
      } else if (event.direction === 2 && !this.$vs.rtl) {
        // Swipe Left
        if (this.isVerticalNavMenuActive && this.showCloseButton) {
          this.isVerticalNavMenuActive = false;
        }
      }
    },
    onSwipeAreaSwipe(event) {
      if (event.direction === 4 && !this.$vs.rtl) {
        // Swipe Right
        if (!this.isVerticalNavMenuActive && this.showCloseButton) {
          this.isVerticalNavMenuActive = true;
        }
      } else if (event.direction === 2 && this.$vs.rtl) {
        // Swipe Left
        if (!this.isVerticalNavMenuActive && this.showCloseButton) {
          this.isVerticalNavMenuActive = true;
        }
      }
    },
    psSectionScroll() {
      const scroll_el = this.$refs.verticalNavMenuPs.$el || this.$refs.verticalNavMenuPs;
      this.showShadowBottom = scroll_el.scrollTop > 0;
    },
    mouseEnter() {
      if (this.reduce) {
        this.$store.commit('UPDATE_VERTICAL_NAV_MENU_ITEMS_MIN', false);
      }
      this.isMouseEnter = true;
    },
    mouseLeave() {
      if (this.reduce) {
        this.$store.commit('UPDATE_VERTICAL_NAV_MENU_ITEMS_MIN', true);
      }
      this.isMouseEnter = false;
    },
    setVerticalNavMenuWidth() {
      if (this.windowWidth > 1200) {
        if (this.layoutType === 'vertical') {
          // Set reduce
          this.reduce = !!this.reduceButton;

          // Open NavMenu
          this.$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', true);

          // Set Menu Items Only Icon Mode
          const verticalNavMenuItemsMin = !!(this.reduceButton && !this.isMouseEnter);
          this.$store.commit('UPDATE_VERTICAL_NAV_MENU_ITEMS_MIN', verticalNavMenuItemsMin);

          // Menu Action buttons
          this.clickNotClose = true;
          this.showCloseButton = false;

          const verticalNavMenuWidth = this.isVerticalNavMenuReduced ? 'reduced' : 'default';
          this.$store.dispatch('updateVerticalNavMenuWidth', verticalNavMenuWidth);

          return;
        }
      }

      // Close NavMenu
      this.$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', false);

      // Reduce button
      if (this.reduceButton) {
        this.reduce = false;
      }

      // Menu Action buttons
      this.showCloseButton = true;
      this.clickNotClose = false;

      // Update NavMenu Width
      this.$store.dispatch('updateVerticalNavMenuWidth', 'no-nav-menu');

      // Remove Only Icon in Menu
      this.$store.commit('UPDATE_VERTICAL_NAV_MENU_ITEMS_MIN', false);

      // if(this.layoutType === 'vertical' || (this.layoutType === 'horizontal' && this.windowWidth < 1200))
      // if (this.windowWidth < 1200) {

      //   // Close NavMenu
      //   this.$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', false)

      //   // Reduce button
      //   if (this.reduceButton) this.reduce = false

      //   // Menu Action buttons
      //   this.showCloseButton = true
      //   this.clickNotClose   = false

      //   // Update NavMenu Width
      //   this.$store.dispatch('updateVerticalNavMenuWidth', 'no-nav-menu')

      //   // Remove Only Icon in Menu
      //   this.$store.commit('UPDATE_VERTICAL_NAV_MENU_ITEMS_MIN', false)

      // } else {

      //   // Set reduce
      //   this.reduce = this.reduceButton ? true : false

      //   // Open NavMenu
      //   this.$store.commit('TOGGLE_IS_VERTICAL_NAV_MENU_ACTIVE', true)

      //   // Set Menu Items Only Icon Mode
      //   const verticalNavMenuItemsMin = (this.reduceButton && !this.isMouseEnter) ? true : false
      //   this.$store.commit('UPDATE_VERTICAL_NAV_MENU_ITEMS_MIN', verticalNavMenuItemsMin)

      //   // Menu Action buttons
      //   this.clickNotClose   = true
      //   this.showCloseButton = false

      //   const verticalNavMenuWidth   = this.isVerticalNavMenuReduced ? "reduced" : "default"
      //   this.$store.dispatch('updateVerticalNavMenuWidth', verticalNavMenuWidth)
      // }
    },
    toggleReduce(val) {
      this.reduceButton = val;
      this.setVerticalNavMenuWidth();
    },
  },
};

</script>

<style lang="scss">
@import "@sass/vuexy/components/verticalNavMenu.scss"
</style>
