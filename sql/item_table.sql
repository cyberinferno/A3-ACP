USE [ASD]
GO

/****** Object:  Table [dbo].[item_table]    Script Date: 06/07/2013 20:05:26 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO

SET ANSI_PADDING ON
GO

CREATE TABLE [dbo].[item_table](
	[item_id] [bigint] NOT NULL,
	[item_name] [varchar](255) NOT NULL,
	[item_category] [varchar](255) NOT NULL,
	[item_code] [varchar](255) NOT NULL,
	[items_count] [bigint] NOT NULL,
	[item_pic] [varchar](255) NOT NULL,
	[buy_credits] [bigint] NOT NULL,
	[rent_credits] [bigint] NULL
) ON [PRIMARY]

GO

SET ANSI_PADDING OFF
GO

