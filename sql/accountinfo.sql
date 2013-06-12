USE [ASD]
GO

/****** Object:  Table [dbo].[AccountInfo]    Script Date: 06/07/2013 19:53:36 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[AccountInfo](
	[account] [char](99) NULL,
	[contact] [char](99) NULL,
	[name] [char](99) NULL,
	[email] [char](100) NULL,
	[ip] [char](50) NOT NULL,
	[login_ip] [char](50) NOT NULL,
	[event_points] [int] NOT NULL,
	[cevent_points] [int] NOT NULL,
	[refresh_count] [int] NOT NULL,
	[ref_add_allow] [int] NOT NULL,
	[referer] [char](99) NULL,
	[flamez_coins] [float] NOT NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

ALTER TABLE [dbo].[AccountInfo] ADD  CONSTRAINT [DF_AccountInfo_ip_1]  DEFAULT ('127.0.0.1') FOR [ip]
GO

ALTER TABLE [dbo].[AccountInfo] ADD  CONSTRAINT [DF_AccountInfo_login_ip]  DEFAULT ('127.0.0.1') FOR [login_ip]
GO

ALTER TABLE [dbo].[AccountInfo] ADD  CONSTRAINT [DF_AccountInfo_gift_points]  DEFAULT ((0)) FOR [event_points]
GO

ALTER TABLE [dbo].[AccountInfo] ADD  CONSTRAINT [DF_AccountInfo_flamez_coins]  DEFAULT ((0)) FOR [flamez_coins]
GO

