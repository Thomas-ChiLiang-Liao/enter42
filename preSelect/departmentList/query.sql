SELECT 
  CONCAT(TVEREDepartment.id, TVERESchool.title) AS depIdTitle,
  TVEREDepartment.examSort AS examSort
  FROM TVEREDepartment
  LEFT JOIN TVERESchool ON TVEREDepartment.schid = TVERESchool.id
  WHERE TVEREDepartment.id = '101002';

SELECT DISTINCT
  class.title AS classTitle,
  RIGHT(student.id,2) AS seatNo,
  student.name AS studentName,
  JSON_EXTRACT(student.scoreG, '$.chinese') AS chinese,
  JSON_EXTRACT(student.scoreG, '$.english') AS english,
  JSON_EXTRACT(student.scoreG, '$.math') AS math,
  JSON_EXTRACT(student.scoreG, '$.pro1') AS pro1,
  JSON_EXTRACT(student.scoreG, '$.pro2.B02') AS pro2,
  (JSON_EXTRACT(student.scoreG, '$.chinese') + JSON_EXTRACT(student.scoreG, '$.english') + JSON_EXTRACT(student.scoreG, '$.math') + JSON_EXTRACT(student.scoreG, '$.pro1') + JSON_EXTRACT(student.scoreG, '$.pro2.B02')) AS total
FROM student
LEFT JOIN class ON class.id = LEFT(student.id,3)
WHERE class.id = '011'
ORDER BY total DESC;

SELECT
  class.title AS classTitle,
  RIGHT(student.id,2) AS seatNo,
  student.name AS stuName,
  student.examId AS examId,
  student.scoreG AS score
FROM student
LEFT JOIN class ON class.id = LEFT(student.id,3)
LEFT JOIN TVERETarget ON student.id = LEFT(TVERETarget.id,6)
WHERE RIGHT(TVERETarget.id,6) = '101009';

#預選校校列表查詢
SELECT
  TVEREDepartment.id AS departmentId,
  CONCAT(TVERESchool.title, TVEREDepartment.title) AS title,
  TVEREDepartment.quotaA AS quotaA,
  CONCAT(YEAR(TVEREDepartment.examDate) - 1911, "年", MONTH(TVEREDepartment.examDate), "月", DAYOFMONTH(TVEREDepartment.examDate), "日 ") AS examDate,
  WEEKDAY(TVEREDepartment.examDate) AS examDateWeekDay,
  TVEREStatic.num as students,
  TVERESchool.maxTargets AS maxTargets
FROM TVEREDepartment
LEFT JOIN TVERESchool ON TVERESchool.id = LEFT(TVEREDepartment.id, 3)
LEFT JOIN TVEREStatic ON TVEREDepartment.id = TVEREStatic.DepartmentID
WHERE TVEREDepartment.examSort = '04' AND TVEREDepartment.quotaA != 0
ORDER BY TVERESchool.isPublic DESC, TVEREDepartment.id ASC;

#以學生id查詢該生選填的結果
SELECT
  TVEREDepartment.id AS departmentId,
  CONCAT(TVERESchool.title, TVEREDepartment.title) AS title,
  TVEREDepartment.quotaA AS quotaA,
  CONCAT(YEAR(TVEREDepartment.examDate) - 1911, "年", MONTH(TVEREDepartment.examDate), "月", DAYOFMONTH(TVEREDepartment.examDate), "日 ") AS examDate,
  WEEKDAY(TVEREDepartment.examDate) AS examDateWeekDay,
  TVEREStatic.num AS students,
  TVERESchool.maxTargets AS maxTargets,
  count(mid(TVERETarget.id,7,3)) AS targets
FROM TVERETarget
LEFT JOIN TVEREDepartment ON RIGHT(TVERETarget.id,6) = TVEREDepartment.id
LEFT JOIN TVERESchool ON MID(TVERETarget.id,7,3) = TVERESchool.id
LEFT JOIN TVEREStatic ON RIGHT(TVERETarget.id,6) = TVEREStatic.DepartmentID
WHERE LEFT(TVERETarget.id,6)= '041001'
ORDER BY TVERESchool.isPublic DESC, TVEREDepartment.id ASC;

#查詢某生各校已選志願數
SELECT
  TVERESchool.id AS schId,
  TVERESchool.title AS schName,
  COUNT(*) AS targets
FROM TVERETarget
LEFT JOIN TVERESchool ON MID(TVERETarget.id,7,3) = TVERESchool.id
WHERE LEFT(TVERETarget.id,6) = '041001'
GROUP BY MID(TVERETarget.id,7,3)