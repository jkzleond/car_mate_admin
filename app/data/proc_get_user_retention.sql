drop proc getUserRetention
;
;
;
go
create proc getUserRetention
    @start_date datetime = null,
    @end_date datetime = null,
    @grain varchar(10) = 'day', -- 时间粒度 day, week, month, year
    @client varchar(50) = null, -- 客户端
    @version varchar(20) = null -- 版本号
as
  set nocount on;
  if (@start_date is null)
    begin
      set @start_date = dateadd(dd, datediff(dd, 7, getdate()), 0);
    end

  if (@end_date is null)
    begin
      set @end_date = dateadd(dd, datediff(dd, 0, getdate()), 0);
    end

  create table #temp(
    id int identity(1, 1) not null,
    date datetime,
    new int,
    c0 int,
    c1 int,
    c2 int,
    c3 int,
    c4 int,
    c5 int,
    c6 int,
    c7 int,
    c8 int,
    c9 int,
    client varchar(50),
    version varchar(30)
  )

  /*根据粒度不同进行初步分组*/
  if (@grain = 'day')
    begin
      with daycte as (
        select dateadd(dd, datediff(dd, 0, @start_date), 0) as day
        union all
        select day + 1
        from daycte
        where day + 1 <= @end_date
      ) --递归查询出天
      select * into #daytemp from daycte
      insert into #temp
        select
          dt.day, count(distinct ur.user_id) as new,
                  max( datediff(dd, ur.day, ul.day) ) as c0,
                  count
                  (
                      case
                      when datediff(dd, ur.day, ul.day) = 1 then
                        1
                      else
                        0
                      end
                  ) as c1,
                  count
                  (
                      case
                      when datediff(dd, ur.day, ul.day) = 2 then
                        1
                      else
                        0
                      end
                  ) as c2,
                  count
                  (
                      case
                      when datediff(dd, ur.day, ul.day) = 3 then
                        1
                      else
                        0
                      end
                  ) as c3,
                  count
                  (
                      case
                      when datediff(dd, ur.day, ul.day) = 4 then
                        1
                      else
                        0
                      end
                  ) as c4,
                  count
                  (
                      case
                      when datediff(dd, ur.day, ul.day) = 5 then
                        1
                      else
                        0
                      end
                  ) as c5,
                  count
                  (
                      case
                      when datediff(dd, ur.day, ul.day) = 6 then
                        1
                      else
                        0
                      end
                  ) as c6,
                  count
                  (
                      case
                      when datediff(dd, ur.day, ul.day) = 7 then
                        1
                      else
                        0
                      end
                  ) as c7,
                  count
                  (
                      case
                      when datediff(dd, ur.day, ul.day) = 14 then
                        1
                      else
                        0
                      end
                  ) as c8,
                  count
                  (
                      case
                      when datediff(dd, ur.day, ul.day) = 30 then
                        1
                      else
                        0
                      end
                  ) as c9,
          ur.client, ur.version
        from
          #daytemp as dt
          left join
          (
            select dateadd(dd, datediff(dd, 0, createdate), 0) as day, userid as user_id, clienttype as client, clientversion as version from IAM_USER
            where createdate > @start_date and createdate < @end_date
          ) as ur on ur.day = dt.day
          left join
          (
            select dateadd(dd, datediff(dd, 0, createdate), 0) as day, userid as user_id from IAM_USEROPLOG
            where createdate > @start_date and createdate < @end_date
            group by dateadd(dd, datediff(dd, 0, createdate), 0), userid
          ) as ul on ul.user_id = ur.user_id
        group by dt.day, ur.client, ur.version
    end
  --
  --   if (@grain = 'week')
  --     begin
  --
  --     end
  --   if (@grain = 'month')
  --     begin
  --
  --     end
  --   if (@grain = 'year')
  --     begin
  --
  --     end

  select dt.day, sum( case when datediff(dd, ur.day, ul.day) = 1 then 1 else 0 end) as c1 from
    #daytemp as dt
    left join
    (
      select dateadd(dd, datediff(dd, 0, createdate), 0) as day, userid as user_id, clienttype as client, clientversion as version from IAM_USER
      where createdate > @start_date and createdate < @end_date
    ) as ur on ur.day = dt.day
    left join
    (
      select dateadd(dd, datediff(dd, 0, createdate), 0) as day, userid as user_id from IAM_USEROPLOG
      where createdate > @start_date and createdate < @end_date
      group by dateadd(dd, datediff(dd, 0, createdate), 0), userid
    ) as ul on ul.user_id = ur.user_id
  group by dt.day, ur.client, ur.version
  order by dt.day asc

/*
  set @sql = '
    select
      data,
      count(is_register) as new,
      count(
        case when is_register = 0 and is_login = 1 and   then
          1
        else
          0
        end
      ) as login
    from #temp
  ';

  set @where = '';

  if (@client is not null)
    begin
      set @where = 'client = ' + @client;
    end

  if (@version is not null and @where != '')
    begin
      set @where += ' and version = ' + @version;
    end
  else if (@version is null)
    begin
      set @where = 'version = ' + @version;
    end

  if (@where != '')
    begin
      set @sql += ' where ' + @where;
    end*/

-- exec(@sql);
go

