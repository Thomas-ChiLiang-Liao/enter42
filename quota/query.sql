SELECT 
  TVERESchool.title AS schName,
  TVEREDepartment.title AS depName,
  CONCAT(TVETExamSort.id, TVETExamSort.sort) AS examSort,
  TVEREDepartment.quotaA AS quota
FROM TVEREDepartment
LEFT JOIN TVERESchool ON TVEREDepartment.schid = TVERESchool.id
LEFT JOIN TVETExamSort ON TVEREDepartment.examSort = TVETExamSort.id
WHERE TVERESchool.title = '國立雲林科技大學';

SELECT 
  UnionQuota.schName AS schName,
  UnionQuota.depTitle AS depName,
  CONCAT(TVETExamSort.id, TVETExamSort.sort) AS examSort,
  UnionQuota.quotaA AS quotaA
FROM UnionQuota
LEFT JOIN TVETExamSort ON LEFT(UnionQuota.id, 2) = TVETExamSort.id
WHERE UnionQuota.schName = '國立雲林科技大學';