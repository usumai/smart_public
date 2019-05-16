<?php include "01_dbcon.php"; ?>
<?php include "02_header.php"; ?>
<?php include "03_menu.php"; ?>
<script src="includes/vue.js"></script>

<br><br><br><br>
<div id="app">
  <form action="post">
    <input type="text" name="names[]" v-model="names[0].name" v-on:input="changed">
    <input type="text" name="names[]" v-model="names[1].name" v-on:input="changed">
    <input type="text" name="names[]" v-model="names[2].name" v-on:input="changed">
  </form>
  {{ names | json }}
</div>





<script>
var app = new Vue({
  el: "#app",
  data: {
    names: [{name:"lucas",Asset:"123456"},{name:"sam"},{name:"jim"}]
  },
  methods: {
    changed: function() {
      console.log(names[1].Asset);
    }
  }
});
</script>









<?php include "04_footer.php"; ?>