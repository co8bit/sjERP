/**
 * Created by Raytine on 2017/6/21.
 */
var $submit = $("#agencySub"),
    $agencyName = $("#agencyName"),
    $agencyPhone = $("#agencyPhone"),
    $agencyPlace = $("#agencyPlace"),
    $inputError=$(".inputError"),
    $form=$("#six-form"),
    $proIntroBtn=$("#proIntroBtn"),
    $cooEarnBtn = $("#cooEarnBtn"),
    $proIntro = $("#productIntroduce"),
    $cooEarn=$("#cooperationEarn"),
    $hint = $("#success-hit");
    $agencyPhone.pass = false;
    $agencyName.pass = false;
    $agencyPlace.pass = false;
$agencyName.on("blur",function () {
    var value = $agencyName.val();
    if(value ===""){
        $inputError.eq(0).text("请输入您的姓名，以便联系您").css("display","true");
        return
    }
    $inputError.eq(0).css("display","none")
    $agencyName.pass = true;
});
$proIntroBtn.on("click",function () {
    $cooEarn.slideUp(500);
    $proIntro.slideDown(500);
});
$cooEarnBtn.on("click",function () {
    $proIntro.slideUp(500);
    $cooEarn.slideDown(500);

});
    $agencyPhone.on("blur",function () {
      var value = $agencyPhone.val();
      if(!/\d{11}/.test(value)){
          $inputError.eq(1).text("手机号码格式错误，请输入11位手机号码").css("display","true");
          return
      }
      $inputError.eq(1).css("display","none")
      $agencyPhone.pass = true;
   });

    $agencyPlace.on("blur",function () {
       var value = $agencyPlace.val();
       if(value ===""){
           $inputError.eq(2).text("请输入您所在区域").css("display","true");
           return
       }
        $inputError.eq(2).css("display","none")
        $agencyPlace.pass = true;
    });
    $submit.on("click",function () {
       if($agencyPlace.pass===true&&$agencyName.pass===true&&$agencyPhone.pass===true){
           var dataType={
                    name:$agencyName.val(),
                    mobile:$agencyPhone.val(),
                    area:$agencyPlace.val()
           };
           request("getAgency",dataType,function (data) {
               console.log(data.EC);
               if(data.EC>0){
                   $form.fadeOut(500,function () {
                       $hint.show(500)
                   })
               }else {
                   alert("error")
               }
           })
       } else {
           $agencyPhone.trigger("blur");
           $agencyName.trigger("blur");
           $agencyPlace.trigger("blur");
       }
});
