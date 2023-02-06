function addTarget(depid,title) {
  if (confirm("確定要將【"+depid+title+"】加入甄選入學預選校系中？")) {
    document.getElementById("addDepartmentIdTitle").value = depid + title;
    document.getElementById("addTargetForm").submit();
  }
}

function deleteTarget(depid,title) {
  if (confirm("確定要將【"+depid+title+"】從甄入學預選校系中移除？")) {
    document.getElementById("deleteDepartmentIdTitle").value = depid + title;
    document.getElementById("deleteTargetForm").submit();
  }
}

function setOptionDisabled(obj,sw) {
  // 設定某組鍵值全部選項為“有效“或是“無效“
  // 第二個參數直接放 true => 無效，false => 有效
  for (var i = 0; i < obj.length; i++) {
    obj[i].disabled = sw;
    if (sw) obj[i].checked = false;
  }
}

function setOption(target,source) {
  // 依據上層的勾選狀況，設定下一層的開啟及 checked / unchecked
  for (var i = 0; i < target.length; i++) {
    target[i].disabled = source[i];
    // 上層鍵值有 checked，下層除了 disable 之外，還要 unchecked
    if (source[i]) target[i].checked = false;
  }
}

function optionChecked(obj) {
  // 檢查某組鍵值選項是否已經有選項被選取
  let flag = false;
  for (var i = 0; i < obj.length; i++ ) if (obj[i].checked) { flag = true; break; }
  return flag;
}

// 每個 checkbox 被按下時會執行此 function
function setOptionState(state,key,index) {
  optionState[key][index] = state;
  // 把同科目後鍵值的狀態設定成 false;
  if (state) {
    //alert('key(' + key + '), index(' + index+ ')');
    for (var i = key+1; i < optionState.length; i++) optionState[i][index] = false;
  }
  switch (key) {
    case 0: // 第一鍵值科目勾選異動
      if (!optionChecked(document.getElementsByClassName('key1'))) {
        // 第一鍵值沒有任選科目被選到
        // 清除 optionState
        for (var i = 0; i < 3; i++) for(var j = 0; j < 6; j++) optionState[i][j] = false;
        // 重排鈕設定成無效
        document.getElementsByClassName('btn')[0].disabled = true;
        // 第n鍵值-科目陣列清除為未選。
        setOptionDisabled(document.getElementsByClassName('key2'),true);
        setOptionDisabled(document.getElementsByClassName('key3'),true);
        setOptionDisabled(document.getElementsByClassName('key4'),true);            
      } else {
        // 第一鍵值有科目被選到
        // 重排鈕設定為有效
        document.getElementsByClassName('btn')[0].disabled = false;
        //依第一鍵值的勾選狀況設定第二鍵值各科目為有效/無效
        setOption(document.getElementsByClassName('key2'), optionState[0]);
      }
      break;
    case 1: // 第二鍵值科目勾選異動
      if (!optionChecked(document.getElementsByClassName('key2'))) {
        // 第二鍵值沒有被選到
        // 清除 optionState
        for (var i = 1; i < 3; i++) for (var j = 0; j < 6; j++) optionState[i][j] = false;
        setOptionDisabled(document.getElementsByClassName('key3'),true);
        setOptionDisabled(document.getElementsByClassName('key4'),true);            
      } else {
        // 第二鍵值有科目被選取，依第一第二鍵選的勾選狀設設定第三鍵值
        var object = document.getElementsByClassName('key3');
        for (var i = 0; i < object.length; i++) object[i].disabled = (optionState[0][i] || optionState[1][i]);
      }
      break;
    case 2: // 第三鍵值科目勾選異動
      if (!optionChecked(document.getElementsByClassName('key3'))) {
        // 第三鍵沒有被選到
        // 清除 optionState
        for (var i = 2; i < 3; i++) for (var j = 0; j < 6; j++) optionState[i][j] = false;
        setOptionDisabled(document.getElementsByClassName('key4'), true);
      } else {
        // 第三鍵值有科目被選取，依第一、第二及第三鍵值的勾選狀況設定第四鍵值
        var object = document.getElementsByClassName('key4');
        for (var i = 0; i < object.length; i++) object[i].disabled = (optionState[0][i] || optionState[1][i] || optionState[2][i])
      }
  }
  //console.log(optionState);
}

function setTable() {
  let tbody = document.getElementById('scoreTable')

  for (let i = 0; i < studentArray.length; i++) {
    let row = document.createElement('tr');
    row.className += 'bg-light';

    let td_no = document.createElement('td');
    td_no.className += 'text-center align-middle';
    td_no.innerHTML = i+1;
    row.appendChild(td_no);

    let td_class = document.createElement('td');
    td_class.className += 'text-center align-middle';
    td_class.innerHTML = studentArray[i].classTitle;
    row.appendChild(td_class);

    // let td_seatNo = document.createElement('td');
    // td_seatNo.className += 'text-center align-middle';
    // td_seatNo.innerHTML = studentArray[i].seatNo;
    // row.appendChild(td_seatNo); 
    
    let td_examId = document.createElement('td');
    td_examId.className += 'text-center align-middle';
    td_examId.innerHTML = studentArray[i].examId;
    row.appendChild(td_examId);            

    // let td_name = document.createElement('td');
    // td_name.className += 'text-center align-middle';
    // td_name.innerHTML = studentArray[i].stuName;
    // row.appendChild(td_name);

    let td_chinese = document.createElement('td');
    td_chinese.className += 'text-center align-middle';
    td_chinese.innerHTML = studentArray[i].chinese;
    row.appendChild(td_chinese); 

    let td_english = document.createElement('td');
    td_english.className += 'text-center align-middle';
    td_english.innerHTML = studentArray[i].english;
    row.appendChild(td_english); 

    let td_math = document.createElement('td');
    td_math.className += 'text-center align-middle';
    td_math.innerHTML = studentArray[i].math;
    row.appendChild(td_math); 

    let td_pro1 = document.createElement('td');
    td_pro1.className += 'text-center align-middle';
    td_pro1.innerHTML = studentArray[i].pro1;
    row.appendChild(td_pro1);

    let td_pro2 = document.createElement('td');
    td_pro2.className += 'text-center align-middle';
    td_pro2.innerHTML = studentArray[i].pro2;
    row.appendChild(td_pro2);   

    let td_total = document.createElement('td');
    td_total.className += 'text-center align-middle';
    td_total.innerHTML = studentArray[i].total;
    row.appendChild(td_total);

    tbody.appendChild(row);                                   
  }
}

function reSort() {
  //console.log(optionState);
  // 在可能重新選擇的狀況下，要把 keyString 中的值清除。
  for (var i = 0; i < keyString.length; i++) { keyString[i].left = ""; keyString[i].right = ""; }

  // 設定每一鍵值條件內容
  for (var i = 0; i < keyString.length; i++) {
    for (var j = 0; j < 6; j++) {
      switch (j) {
        case 0:
          if (optionState[i][j]) {
            ( keyString[i].left.length == 0 ? keyString[i].left += 'parseInt(a.chinese)' : keyString[i].left += ' + parseInt(a.chinese)');
            ( keyString[i].right.length == 0 ? keyString[i].right += 'parseInt(b.chinese)' : keyString[i].right += ' + parseInt(b.chinese)');
          }
          break;
        case 1:
          if (optionState[i][j]) {
            ( keyString[i].left.length == 0 ? keyString[i].left += 'parseInt(a.english)' : keyString[i].left += ' + parseInt(a.english)');
            ( keyString[i].right.length == 0 ? keyString[i].right += 'parseInt(b.english)' : keyString[i].right += ' + parseInt(b.english)');
          }
          break;
        case 2:
          if (optionState[i][j]) {
            ( keyString[i].left.length == 0 ? keyString[i].left += 'parseInt(a.math)' : keyString[i].left += ' + parseInt(a.math)');
            ( keyString[i].right.length == 0 ? keyString[i].right += 'parseInt(b.math)' : keyString[i].right += ' + parseInt(b.math)');
          }
          break;
        case 3:
          if (optionState[i][j]) {
            ( keyString[i].left.length == 0 ? keyString[i].left += 'parseInt(a.pro1)' : keyString[i].left += ' + parseInt(a.pro1)');
            ( keyString[i].right.length == 0 ? keyString[i].right += 'parseInt(b.pro1)' : keyString[i].right += ' + parseInt(b.pro1)');
          }
          break;
        case 4:
          if (optionState[i][j]) {
            ( keyString[i].left.length == 0 ? keyString[i].left += 'parseInt(a.pro2)' : keyString[i].left += ' + parseInt(a.pro2)');
            ( keyString[i].right.length == 0 ? keyString[i].right += 'parseInt(b.pro2)' : keyString[i].right += ' + parseInt(b.pro2)');
          }
          break;
        case 5:
          if (optionState[i][j]) {
            ( keyString[i].left.length == 0 ? keyString[i].left += 'parseInt(a.total)' : keyString[i].left += ' + parseInt(a.total)');
            ( keyString[i].right.length == 0 ? keyString[i].right += 'parseInt(b.total)' : keyString[i].right += ' + parseInt(b.total)');
          }
          break;
      }
    }
  }
  //console.log(keyString);
  
  // 排序
  studentArray.sort(function (a,b) {
    if ( eval(keyString[0].left + '==' + keyString[0].right) ) {
      // 第一鍵值相同
      if (keyString[1].left != '') {
        // 比第二鍵值
        if ( eval(keyString[1].left + '==' + keyString[1].right) ) {
          // 第二鍵值相同
          if (keyString[2].left != '') {
            // 比第三鍵值
            if ( eval(keyString[2].left + '==' + keyString[2].right) ) {
              // 第三鍵值相同
              if ( keyString[3].left != '' ) {
                // 比第四鍵值
                if ( eval(keyString[3].left + '==' + keyString[3].right) ) return 0;
                else if ( eval(keyString[3].left + '>' + keyString[3].right) ) return -1;
                     else return 1;
              } return 0;
            }
            else if ( eval(keyString[2].left + '>' + keyString[2].right) ) return -1;
                 else return 1;
          } return 0;
        }
        else if ( eval(keyString[1].left + '>' + keyString[1].right) ) return -1;
             else return 1;
      } else return 0;
    } 
    else if ( eval(keyString[0].left + '>' + keyString[0].right) ) return -1;
         else return 1;
  });
  

  // 清除顯示
  let content = document.getElementsByTagName('tbody');
  while (content[0].lastElementChild) content[0].removeChild(content[0].lastElementChild);
  // 重新顯示表格-排序後
  setTable();
  $("#sortOptionPanel").slideUp("slow");
}

$(document).ready(function(){
  $("#resortPanel").click(function(){
    $("#sortOptionPanel").slideToggle("slow");
  })
})