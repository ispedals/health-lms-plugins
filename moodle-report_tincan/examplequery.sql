SELECT
    tg.name,
    tg.course,
    tg.type,
    tg.score,
    DATE_FORMAT(FROM_UNIXTIME(gg.timemodified),
    '%Y-%m-%d') AS 'Updated' 
FROM
    prefix_report_tincan_grades AS tg  
JOIN
    prefix_course AS c          
        ON c.id = tg.courseid  
JOIN
    prefix_course_categories AS cc          
        ON cc.id = c.category  
JOIN
    prefix_grade_grades AS gg                  
        ON gg.userid = tg.userid  
JOIN
    prefix_grade_items AS gi                  
        ON gi.id = gg.itemid          
WHERE
    gi.id= tg.itemid
%%FILTER_SYSTEMUSER:tg.userid%%
%%FILTER_COURSES:tg.courseid%%
%%FILTER_CATEGORIES:cc.path%%  
ORDER BY
    tg.name ASC