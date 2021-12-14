SELECT 
  TVEREDepartment.id AS depid,
  CONCAT(TVERESchool.title, TVEREDepartment.title) AS title, 
  TVERESchool.isRestricted AS isRestricted,
  TVEREDepartment.quotaA AS quotaA, TVEREDepartment.stage2QuotaA AS stage2QuotaA,
  TVEREDepartment.examDate AS examDate
FROM TVEREDepartment
LEFT JOIN TVERESchool ON TVEREDepartment.schoolID = TVERESchool.id
WHERE TVEREDepartment.examSort = '04'
ORDER BY TVERESchool.isPublic DESC, TVEREDepartment.id ASC;

SELECT 
  TVEREDepartment.id AS id, 
  TVERESchool.title AS schTitle, TVEREDepartment.title AS depTitle, 
  CONCAT(TVETExamSort.id, TVETExamSort.sort) AS examSort, 
  TVEREDepartment.quotaA, TVEREDepartment.stage2QuotaA, 
  TVEREDepartment.chineseMagnification, TVEREDepartment.englishMagnification, TVEREDepartment.mathMagnification, 
  TVEREDepartment.pro1Magnification, TVEREDepartment.pro2Magnification, TVEREDepartment.totalMagnification, 
  TVEREDepartment.chineseWeight, TVEREDepartment.englishWeight, TVEREDepartment.mathWeight, 
  TVEREDepartment.pro1Weight, TVEREDepartment.pro2Weight, TVEREDepartment.examScoreRate, 
  TVEREDepartment.assignItem1, TVEREDepartment.assignItem1Threshold, TVEREDepartment.assignItem1Rate, 
  TVEREDepartment.assignItem2, TVEREDepartment.assignItem2Threshold, TVEREDepartment.assignItem2Rate, 
  TVEREDepartment.assignItem3, TVEREDepartment.assignItem3Threshold, TVEREDepartment.assignItem3Rate, 
  TVEREDepartment.assignItem4, TVEREDepartment.assignItem4Threshold, TVEREDepartment.assignItem4Rate, 
  TVEREDepartment.assignItem5, TVEREDepartment.assignItem5Threshold, TVEREDepartment.assignItem5Rate, 
  TVEREDepartment.assignItemExamFee, TVEREDepartment.assignItemCount, TVEREDepartment.certificateExtra, 
  TVEREDepartment.sequence1, TVEREDepartment.sequence2, TVEREDepartment.sequence3, 
  TVEREDepartment.sequence4, TVEREDepartment.sequence5, TVEREDepartment.sequence6, TVEREDepartment.sequenceCount, 
  TVEREDepartment.date1, TVEREDepartment.date2, TVEREDepartment.examDate, TVEREDepartment.date3, 
  TVEREDepartment.date4, TVEREDepartment.date5, TVEREDepartment.date6, TVEREDepartment.checkInDate, 
  TVEREDepartment.B1, TVEREDepartment.B2, 
  TVEREDepartment.C1, TVEREDepartment.C2, TVEREDepartment.C3, TVEREDepartment.C4, TVEREDepartment.C5, TVEREDepartment.C6, 
  TVEREDepartment.C7, TVEREDepartment.C8, TVEREDepartment.C_counts, 
  TVEREDepartment.uploadMemo, TVEREDepartment.assignExamMemo, TVEREDepartment.memo 
FROM TVEREDepartment 
LEFT JOIN TVERESchool ON TVEREDepartment.schid = TVERESchool.id 
LEFT JOIN TVETExamSort ON TVEREDepartment.examSort = TVETExamSort.id 
WHERE TVEREDepartment.id = '101004';