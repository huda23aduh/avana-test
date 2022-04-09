SELECT
  e.id as order_no,
  TO_CHAR(e.order_date, 'Month d, YYYY') as order_date,
  i.name AS customer,
  SUM(e.sum_amount) AS amount,
  CASE WHEN sum(p.sum_amount) is not NULL
  AND sum(e.sum_amount) - sum(p.sum_amount) = 0 THEN 0 WHEN sum(p.sum_amount) is NULL THEN abs(
    sum(e.sum_amount)
  ) ELSE abs(
    sum(e.sum_amount) - sum(p.sum_amount)
  ) END AS amount_due,
  CASE WHEN sum(p.sum_amount) is not NULL
  AND (
    sum(e.sum_amount) - sum(p.sum_amount) = 0
  ) THEN 'Paid' ELSE 'Outstanding' END AS status
FROM
  customers i
  LEFT JOIN (
    SELECT
      id,
      customer_id,
      order_date,
      SUM(sub_total) AS sum_amount
    FROM
      orders
    GROUP BY
      id,
      order_date
  ) e ON e.customer_id = i.id
  LEFT JOIN (
    SELECT
      order_id,
      SUM(amount_paid) AS sum_amount
    FROM
      payments
    WHERE
      status = 1
    GROUP BY
      1
  ) p ON p.order_id = e.id
GROUP BY
  order_no,
  i.name,
  e.order_date
ORDER by
  order_no;
