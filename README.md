# SIMPLE FORECAST SERVICE FOR FOOTBALL TOURNAMENTS

Bootstrap v2.3.2
JQuery

## HOW TO LAUNCH ON THE SERVER

```
git clone https://github.com/vl-tk/football_forecasts

docker-compose up -d
```

## What it is?

This service was used among friends and colleagues only so it's very basic and
the code is old, common operations aren't performed very conveniently (and who
knows about security bugs missed) but nevertheless it was fun to use this
service while following championships with minimal maintanence so I decided to
publish it for anyone interested.

### Preparation before tournament

Tournament games should be added before the tournament in the database.

### adminer

http://localhost:8081/?server=mysql&username=main&db=main

### TODO AND IDEAS

удаление членов группы админом
удаление себя из группы если не овнер

бот самого популярного прогноза

