export default {
  mounted() {
    this.val = this.value;

    // Make sure the "rawVal" is 'unescaped'
    let node = document.createElement('textarea');
    node.innerHTML = this.value;
    this.rawVal = node.value;
  },
  data: () => {
    return {
      val: null,
      rawVal: null,
    };
  },
};
