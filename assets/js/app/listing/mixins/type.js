export default {
  computed:{
    type(){
      return this.$store.getters['general/getType'];
    }
  }
}