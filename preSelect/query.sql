SELECT
  IF((switch), "ON", "OFF") AS sw1,
  IF(((now() - INTERVAL 28800 SECOND) < expire), "ON", "OFF") AS sw2,
  expire + INTERVAL 28800 SECOND AS expireDate
  FROM control
  WHERE 1;